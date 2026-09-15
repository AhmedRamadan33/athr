<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreCheckoutRequest;
use App\Models\Address;
use App\Services\Admin\SettingService;
use App\Services\OrderService;
use App\Services\Payment\PaymobService;
use App\Services\Storefront\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService $cartService,
        protected OrderService $orderService,
        protected PaymobService $paymobService,
        protected SettingService $settings,
    ) {}

    public function create(Request $request): View|RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $cart = $this->cartService->currentCart($customer->id, $request->session()->getId());

        if ($cart->items->isEmpty()) {
            return redirect()->route('storefront.cart.index')->with('error', 'السلة فارغة.');
        }

        return view('storefront.checkout.index', [
            'cart' => $cart,
            'addresses' => $customer->addresses,
            'freeShippingThreshold' => $this->settings->freeShippingThreshold(),
        ]);
    }

    public function store(StoreCheckoutRequest $request): RedirectResponse
    {
        $customer = Auth::guard('customer')->user();
        $cart = $this->cartService->currentCart($customer->id, $request->session()->getId());
        $address = Address::findOrFail($request->validated()['address_id']);

        $order = $this->orderService->placeOrder(
            $customer,
            $cart,
            $address,
            $request->validated()['payment_method'],
            $request->validated()['coupon_code'] ?? null,
            $request->validated()['notes'] ?? null,
        );

        if ($order->payment_method === 'paymob') {
            $iframeUrl = $this->paymobService->createPaymentIframeUrl($order);

            return redirect()->away($iframeUrl);
        }

        return redirect()->route('storefront.orders.show', $order->order_number)
            ->with('success', 'تم تسجيل طلبك بنجاح، رقم الطلب: '.$order->order_number);
    }
}
