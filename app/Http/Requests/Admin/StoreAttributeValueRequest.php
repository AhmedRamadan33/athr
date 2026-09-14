<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('catalog.manage');
    }

    public function rules(): array
    {
        return [
            'value' => [
                'required', 'string', 'max:255',
                Rule::unique('attribute_values', 'value')->where('attribute_id', $this->route('attribute')->id),
            ],
        ];
    }
}
