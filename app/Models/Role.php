<?php

namespace App\Models;

use App\Traits\LogsActivityInArabic;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use LogsActivity, LogsActivityInArabic {
        LogsActivityInArabic::getDescriptionForEvent insteadof LogsActivity;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('roles');
    }

    protected function activitySubjectLabel(): string
    {
        return 'الدور "'.$this->name.'"';
    }
}
