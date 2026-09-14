<?php

namespace App\Http\Requests\Admin;

use App\Support\EgyptGovernorates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('shipping.manage');
    }

    public function rules(): array
    {
        return [
            'governorate' => [
                'required', Rule::in(EgyptGovernorates::LIST),
                Rule::unique('shipping_zones', 'governorate')->ignore($this->route('shipping_zone')),
            ],
            'cost' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
