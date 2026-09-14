<?php

namespace App\Http\Requests\Customer;

use App\Support\EgyptGovernorates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('customer') !== null;
    }

    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:100'],
            'governorate' => ['required', Rule::in(EgyptGovernorates::LIST)],
            'city' => ['required', 'string', 'max:255'],
            'address_line' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:20'],
            'is_default' => ['boolean'],
        ];
    }
}
