<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('catalog.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:brands,name'],
            'logo' => ['nullable', 'image', 'max:5120'],
            'is_active' => ['boolean'],
        ];
    }
}
