<?php

namespace App\Services\Storefront;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function __construct(protected CartRepositoryInterface $carts) {}

    public function currentCart(?int $customerId, ?string $sessionId): Cart
    {
        if ($customerId) {
            return $this->carts->findByCustomer($customerId)
                ?? $this->carts->create(['customer_id' => $customerId]);
        }

        return $this->carts->findBySession($sessionId)
            ?? $this->carts->create(['session_id' => $sessionId]);
    }

    public function addItem(Cart $cart, ProductVariant $variant, int $quantity): CartItem
    {
        $existing = $cart->items()->where('product_variant_id', $variant->id)->first();
        $newQuantity = $quantity + ($existing?->quantity ?? 0);

        if ($newQuantity > $variant->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'الكمية المطلوبة غير متوفرة فى المخزون.',
            ]);
        }

        if ($existing) {
            $existing->update(['quantity' => $newQuantity, 'price' => $variant->price]);

            return $existing;
        }

        return $cart->items()->create([
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'price' => $variant->price,
        ]);
    }

    public function updateItemQuantity(CartItem $item, int $quantity): void
    {
        if ($quantity > $item->productVariant->stock_quantity) {
            throw ValidationException::withMessages([
                'quantity' => 'الكمية المطلوبة غير متوفرة فى المخزون.',
            ]);
        }

        $item->update(['quantity' => $quantity]);
    }

    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function mergeGuestIntoCustomer(string $sessionId, int $customerId): Cart
    {
        $guestCart = $this->carts->findBySession($sessionId);

        if (! $guestCart) {
            return $this->currentCart($customerId, null);
        }

        $customerCart = $this->carts->findByCustomer($customerId);

        if (! $customerCart) {
            $guestCart->update(['customer_id' => $customerId, 'session_id' => null]);

            return $guestCart->fresh();
        }

        foreach ($guestCart->items as $item) {
            $this->addItem($customerCart, $item->productVariant, $item->quantity);
        }

        $guestCart->delete();

        return $customerCart->fresh();
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
