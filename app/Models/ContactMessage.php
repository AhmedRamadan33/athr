<?php

namespace App\Models;

use App\Traits\LogsActivityInArabic;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ContactMessage extends Model
{
    use LogsActivity, LogsActivityInArabic {
        LogsActivityInArabic::getDescriptionForEvent insteadof LogsActivity;
    }

    protected $fillable = [
        'name',
        'email',
        'message',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['is_read'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('contact_messages');
    }

    protected function activitySubjectLabel(): string
    {
        return 'رسالة التواصل من "'.$this->name.'"';
    }
}
