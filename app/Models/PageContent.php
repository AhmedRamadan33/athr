<?php

namespace App\Models;

use App\Traits\LogsActivityInArabic;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PageContent extends Model
{
    use LogsActivity, LogsActivityInArabic {
        LogsActivityInArabic::getDescriptionForEvent insteadof LogsActivity;
    }

    protected $fillable = [
        'page_key',
        'field_key',
        'value',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['page_key', 'field_key', 'value'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('page_contents');
    }

    protected function activitySubjectLabel(): string
    {
        return 'محتوى صفحة "'.$this->page_key.'" — '.$this->field_key;
    }
}
