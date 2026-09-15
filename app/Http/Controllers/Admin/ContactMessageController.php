<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\Admin\ContactMessageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct(protected ContactMessageService $contactMessages) {}

    public function index(Request $request): View
    {
        return view('admin.contact-messages.index', [
            'messages' => $this->contactMessages->paginate($request->only(['search', 'status'])),
        ]);
    }

    public function markAsRead(ContactMessage $contactMessage): RedirectResponse
    {
        $this->contactMessages->markAsRead($contactMessage);

        return back()->with('success', 'تم تحديد الرسالة كمقروءة.');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $this->contactMessages->delete($contactMessage);

        return back()->with('success', 'تم حذف الرسالة بنجاح.');
    }
}
