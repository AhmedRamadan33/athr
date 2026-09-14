<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Notifications\AdminRoleUpdatedNotification;
use App\Notifications\NewAdminCreatedNotification;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AdminUserService
{
    public function __construct(protected AdminRepositoryInterface $admins) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->admins->paginate($filters, 10);
    }

    public function create(array $data): Admin
    {
        $admin = $this->admins->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $admin->syncRoles($data['roles'] ?? []);

        Notification::send($this->superAdminsExcept($admin), new NewAdminCreatedNotification($admin));

        return $admin;
    }

    public function update(Admin $admin, array $data): Admin
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'is_active' => $data['is_active'] ?? $admin->is_active,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $this->admins->update($admin, $payload);

        $newRoles = $data['roles'] ?? [];
        $rolesChanged = $admin->getRoleNames()->sort()->values()->all() !== collect($newRoles)->sort()->values()->all();

        if ($rolesChanged) {
            $admin->syncRoles($newRoles);
            $admin->notify(new AdminRoleUpdatedNotification($admin));
        }

        return $admin->fresh();
    }

    public function delete(Admin $admin): void
    {
        if ($admin->id === Auth::guard('admin')->id()) {
            throw ValidationException::withMessages([
                'admin' => 'لا يمكنك حذف حسابك الحالي.',
            ]);
        }

        $this->admins->delete($admin);
    }

    public function toggleActive(Admin $admin): Admin
    {
        return $this->admins->update($admin, ['is_active' => ! $admin->is_active]);
    }

    protected function superAdminsExcept(Admin $except)
    {
        return Admin::role('Super Admin')->where('id', '!=', $except->id)->get();
    }
}
