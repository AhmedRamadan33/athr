<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    const URL_FIELDS = ['facebook_url', 'twitter_url', 'instagram_url', 'tiktok_url'];

    public function authorize(): bool
    {
        return $this->user('admin')->can('settings.manage');
    }

    public function rules(): array
    {
        $rules = [
            'values' => ['required', 'array'],
            'values.*' => ['nullable', 'string', 'max:2000'],
        ];

        foreach (self::URL_FIELDS as $field) {
            $rules["values.{$field}"] = ['nullable', 'url', 'max:2000'];
        }

        return $rules;
    }
}
