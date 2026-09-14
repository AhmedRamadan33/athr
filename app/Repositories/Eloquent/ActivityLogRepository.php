<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\ActivityLogRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Activitylog\Models\Activity;

class ActivityLogRepository extends BaseRepository implements ActivityLogRepositoryInterface
{
    public function __construct(Activity $model)
    {
        parent::__construct($model);
    }

    protected function applyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->with('causer')
            ->when($filters['search'] ?? null, function (Builder $q, string $search) {
                $q->where('description', 'like', "%{$search}%");
            })
            ->when($filters['log_name'] ?? null, function (Builder $q, string $logName) {
                $q->where('log_name', $logName);
            })
            ->when($filters['causer_id'] ?? null, function (Builder $q, string $causerId) {
                $q->where('causer_id', $causerId)->where('causer_type', \App\Models\Admin::class);
            })
            ->when($filters['from'] ?? null, function (Builder $q, string $from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to'] ?? null, function (Builder $q, string $to) {
                $q->whereDate('created_at', '<=', $to);
            });
    }
}
