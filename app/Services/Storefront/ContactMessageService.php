<?php

namespace App\Services\Storefront;

use App\Models\Admin;
use App\Models\ContactMessage;
use App\Notifications\NewContactMessageNotification;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Support\Facades\Notification;

class ContactMessageService
{
    public function __construct(protected ContactMessageRepositoryInterface $contactMessages) {}

    public function submit(array $data): ContactMessage
    {
        $contactMessage = $this->contactMessages->create($data);

        Notification::send(Admin::permission('pages.manage')->get(), new NewContactMessageNotification($contactMessage));

        return $contactMessage;
    }
}
