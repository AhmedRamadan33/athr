<?php

namespace App\Models;

use App\Traits\LogsActivityInArabic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ShippingZone extends Model
{
    use HasFactory, LogsActivity, LogsActivityInArabic {
        LogsActivityInArabic::getDescriptionForEvent insteadof LogsActivity;
    }

    protected $fillable = [
        'governorate',
        'cost',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['governorate', 'cost', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('shipping_zones');
    }

    protected function activitySubjectLabel(): string
    {
        return 'منطقة شحن "'.$this->governorate.'"';
    }
}
