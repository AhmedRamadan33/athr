<?php

namespace App\Services;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Notifications\LowStockNotification;
use App\Notifications\NewOrderNotification;
use App\Notifications\OrderCancelledByCustomerNotification;
use App\Notifications\OrderStatusChangedNotification;
use App\Repositories\Contracts\CouponRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ShippingZoneRepositoryInterface;
use App\Services\Admin\SettingService;
use App\Services\Storefront\CartService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $orders,
        protected CouponRepositoryInterface $coupons,
        protected ShippingZoneRepositoryInterface $shippingZones,
        protected CartService $cartService,
        protected SettingService $settings,
    ) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->orders->paginate($filters, 15);
    }

    public function placeOrder(Customer $customer, Cart $cart, Address $address, string $paymentMethod, ?string $couponCode, ?string $notes): Order
    {
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'السلة فارغة.']);
        }

        foreach ($cart->items as $item) {
            if ($item->quantity > $item->productVariant->stock_quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'المنتج "'.$item->productVariant->product->name.'" غير متوفر بالكمية المطلوبة.',
                ]);
            }
        }

        $shippingZone = $this->shippingZones->findByGovernorate($address->governorate);

        if (! $shippingZone) {
            throw ValidationException::withMessages([
                'address' => 'الشحن غير متاح لهذه المحافظة حاليًا.',
            ]);
        }

        $subtotal = (float) $cart->total();

        $coupon = null;
        $discount = 0.0;

        if ($couponCode) {
            $coupon = $this->coupons->findByCode($couponCode);

            if (! $coupon || ! $coupon->isUsable($subtotal)) {
                throw ValidationException::withMessages([
                    'coupon' => 'كود الخصم غير صالح أو منتهى الصلاحية.',
                ]);
            }

            $discount = $coupon->discountFor($subtotal);
        }

        $freeShippingThreshold = $this->settings->freeShippingThreshold();
        $shippingCost = $freeShippingThreshold !== null && $subtotal >= $freeShippingThreshold
            ? 0.0
            : (float) $shippingZone->cost;
        $total = $subtotal - $discount + $shippingCost;

        return DB::transaction(function () use ($customer, $cart, $address, $paymentMethod, $notes, $coupon, $subtotal, $discount, $shippingCost, $total) {
            $order = $this->orders->create([
                'order_number' => $this->generateOrderNumber(),
                'customer_id' => $customer->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_cost' => $shippingCost,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'payment_status' => 'pending',
                'address_id' => $address->id,
                'coupon_id' => $coupon?->id,
                'notes' => $notes,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->productVariant->product->name,
                    'variant_label' => $item->productVariant->label(),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->price * $item->quantity,
                ]);

                $this->decrementStockAndCheckLowStock($item->productVariant, $item->quantity);
            }

            $order->statusHistories()->create(['status' => 'pending']);

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $this->cartService->clear($cart);

            Notification::send(Admin::permission('orders.manage')->get(), new NewOrderNotification($order));

            return $order->fresh(['items']);
        });
    }

    public function updateStatus(Order $order, string $newStatus, ?string $note, Admin $admin): Order
    {
        if (! in_array($newStatus, $order->allowedNextStatuses(), true)) {
            throw ValidationException::withMessages([
                'status' => 'لا يمكن تغيير الحالة من "'.$order->statusLabel().'" إلى هذه الحالة.',
            ]);
        }

        return DB::transaction(function () use ($order, $newStatus, $note, $admin) {
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    $item->productVariant?->increment('stock_quantity', $item->quantity);
                }
            }

            $payload = ['status' => $newStatus];

            if ($newStatus === 'delivered' && $order->payment_method === 'cod') {
                $payload['payment_status'] = 'paid';
            }

            $this->orders->update($order, $payload);

            $order->statusHistories()->create([
                'status' => $newStatus,
                'note' => $note,
                'changed_by_admin_id' => $admin->id,
            ]);

            $order->customer->notify(new OrderStatusChangedNotification($order));

            return $order->fresh();
        });
    }

    public function cancelByCustomer(Order $order): Order
    {
        if ($order->status !== 'pending') {
            throw ValidationException::withMessages([
                'status' => 'لا يمكن إلغاء الطلب بعد بدء تجهيزه، برجاء التواصل مع خدمة العملاء.',
            ]);
        }

        return DB::transaction(function () use ($order) {
            foreach ($order->items as $item) {
                $item->productVariant?->increment('stock_quantity', $item->quantity);
            }

            $this->orders->update($order, ['status' => 'cancelled']);

            $order->statusHistories()->create([
                'status' => 'cancelled',
                'note' => 'ألغى العميل الطلب بنفسه.',
            ]);

            Notification::send(Admin::permission('orders.manage')->get(), new OrderCancelledByCustomerNotification($order));

            return $order->fresh();
        });
    }

    public function recordPaymobCallback(Order $order, array $payload, bool $success): void
    {
        $order->payments()->create([
            'gateway' => 'paymob',
            'transaction_id' => $payload['id'] ?? null,
            'amount' => isset($payload['amount_cents']) ? $payload['amount_cents'] / 100 : $order->total,
            'status' => $success ? 'paid' : 'failed',
            'raw_response' => $payload,
        ]);

        $this->orders->update($order, [
            'payment_status' => $success ? 'paid' : 'failed',
        ]);
    }

    protected function decrementStockAndCheckLowStock(ProductVariant $variant, int $quantity): void
    {
        $threshold = $this->settings->lowStockThreshold();
        $stockBefore = $variant->stock_quantity;

        $variant->decrement('stock_quantity', $quantity);

        if ($stockBefore > $threshold && $variant->fresh()->stock_quantity <= $threshold) {
            Notification::send(Admin::permission('catalog.manage')->get(), new LowStockNotification($variant->fresh()));
        }
    }

    protected function generateOrderNumber(): string
    {
        do {
            $number = 'ATHR-'.now()->format('Ymd').'-'.strtoupper(Str::random(4));
        } while ($this->orders->query()->where('order_number', $number)->exists());

        return $number;
    }
}
