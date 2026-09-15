<?php

namespace App\Models;

use App\Traits\LogsActivityInArabic;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NewsletterSubscriber extends Model
{
    use LogsActivity, LogsActivityInArabic {
        LogsActivityInArabic::getDescriptionForEvent insteadof LogsActivity;
    }

    protected $fillable = [
        'email',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'unsubscribed_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('unsubscribed_at');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['email'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('newsletter_subscribers');
    }

    protected function activitySubjectLabel(): string
    {
        return 'مشترك النشرة البريدية "'.$this->email.'"';
    }
}
