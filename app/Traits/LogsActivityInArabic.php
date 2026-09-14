<?php

namespace App\Traits;

trait LogsActivityInArabic
{
    public function getDescriptionForEvent(string $eventName): string
    {
        $subject = $this->activitySubjectLabel();

        return match ($eventName) {
            'created' => "تم إنشاء {$subject}",
            'updated' => "تم تحديث {$subject}",
            'deleted' => "تم حذف {$subject}",
            default => $eventName,
        };
    }

    protected function activitySubjectLabel(): string
    {
        return class_basename($this);
    }
}
