<?php

namespace App\Http\Requests\Admin;

use App\Support\EgyptGovernorates;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreShippingZoneRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('shipping.manage');
    }

    public function rules(): array
    {
        return [
            'governorate' => ['required', Rule::in(EgyptGovernorates::LIST), 'unique:shipping_zones,governorate'],
            'cost' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
