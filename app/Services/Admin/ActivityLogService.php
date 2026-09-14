<?php

namespace App\Services\Admin;

use App\Models\Admin;
use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ActivityLogService
{
    public function __construct(protected ActivityLogRepositoryInterface $activityLogs) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->activityLogs->paginate($filters, 15);
    }

    public function logNames(): Collection
    {
        return $this->activityLogs->query()->select('log_name')->distinct()->pluck('log_name');
    }

    public function causers(): Collection
    {
        return Admin::orderBy('name')->get(['id', 'name']);
    }
}
