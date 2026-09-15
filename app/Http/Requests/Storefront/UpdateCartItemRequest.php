<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $cart = $this->route('item')?->cart;

        if (! $cart) {
            return false;
        }

        return Auth::guard('customer')->check()
            ? $cart->customer_id === Auth::guard('customer')->id()
            : $cart->session_id === $this->session()->getId();
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ];
    }
}
