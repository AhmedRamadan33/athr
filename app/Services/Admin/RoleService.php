<?php

namespace App\Services\Admin;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use App\Models\Role;

class RoleService
{
    const PROTECTED_ROLE = 'Super Admin';

    public function __construct(protected RoleRepositoryInterface $roles) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->roles->paginate($filters, 10);
    }

    public function create(array $data): Role
    {
        $role = $this->roles->create([
            'name' => $data['name'],
            'guard_name' => 'admin',
        ]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }

    public function update(Role $role, array $data): Role
    {
        $this->guardProtectedRole($role);

        $this->roles->update($role, ['name' => $data['name']]);
        $role->syncPermissions($data['permissions'] ?? []);

        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $this->guardProtectedRole($role);

        $this->roles->delete($role);
    }

    protected function guardProtectedRole(Role $role): void
    {
        if ($role->name === self::PROTECTED_ROLE) {
            throw ValidationException::withMessages([
                'role' => 'لا يمكن تعديل أو حذف دور "'.self::PROTECTED_ROLE.'".',
            ]);
        }
    }
}
