<?php

namespace App\Services\Admin;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminNotificationService
{
    public function paginate(Admin $admin, int $perPage = 15): LengthAwarePaginator
    {
        return $admin->notifications()->paginate($perPage);
    }

    public function unreadCount(Admin $admin): int
    {
        return $admin->unreadNotifications()->count();
    }

    public function markAsRead(Admin $admin, string $notificationId): void
    {
        $admin->notifications()->where('id', $notificationId)->first()?->markAsRead();
    }

    public function markAllAsRead(Admin $admin): void
    {
        $admin->unreadNotifications->markAsRead();
    }
}
