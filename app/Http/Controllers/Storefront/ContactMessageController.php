<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Storefront\StoreContactMessageRequest;
use App\Services\Storefront\ContactMessageService;
use Illuminate\Http\RedirectResponse;

class ContactMessageController extends Controller
{
    public function __construct(protected ContactMessageService $contactMessages) {}

    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $this->contactMessages->submit($request->validated());

        return back()->with('success', 'تم إرسال رسالتك بنجاح، سنتواصل معك قريبًا.');
    }
}
