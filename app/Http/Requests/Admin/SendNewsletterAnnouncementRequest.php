<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendNewsletterAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user('admin')->can('pages.manage');
    }

    public function rules(): array
    {
        return [
            'subject' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
        ];
    }
}
