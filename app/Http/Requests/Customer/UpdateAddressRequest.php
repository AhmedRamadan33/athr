<?php

namespace App\Http\Requests\Customer;

use App\Support\EgyptGovernorates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('address')?->customer_id === $this->user('customer')?->id;
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
