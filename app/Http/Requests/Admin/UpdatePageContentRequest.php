<?php

namespace App\Http\Requests\Admin;

use App\Services\Admin\PageContentService;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePageContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('pages.manage');
    }

    public function rules(): array
    {
        $fields = PageContentService::PAGES[$this->route('pageKey')]['fields'] ?? [];
        $rules = [];

        foreach ($fields as $key => $config) {
            $rules["values.{$key}"] = $config['type'] === 'image'
                ? ['nullable', 'image', 'max:5120']
                : ['nullable', 'string', 'max:5000'];
        }

        return $rules;
    }
}
