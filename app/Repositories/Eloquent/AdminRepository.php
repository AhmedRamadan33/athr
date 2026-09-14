<?php

namespace App\Repositories\Eloquent;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with('roles')
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where(function (Builder $q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(filled($filters['status'] ?? null), function (Builder $q) use ($filters) {
                $q->where('is_active', $filters['status'] === 'active');
            })
            ->when($filters['role'] ?? null, function (Builder $q, string $role) {
                $q->whereHas('roles', fn (Builder $q) => $q->where('name', $role));
            });
    }
}
