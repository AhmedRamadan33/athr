<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\AdminNotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __construct(protected AdminNotificationService $notifications) {}

    public function index(Request $request): View
    {
        return view('admin.notifications.index', [
            'notifications' => $this->notifications->paginate($request->user('admin')),
        ]);
    }

    public function markAsRead(Request $request, string $notification): RedirectResponse
    {
        $this->notifications->markAsRead($request->user('admin'), $notification);

        return back();
    }

    public function markAllAsRead(Request $request): RedirectResponse
    {
        $this->notifications->markAllAsRead($request->user('admin'));

        return back()->with('success', 'تم تحديد كل الإشعارات كمقروءة.');
    }
}
