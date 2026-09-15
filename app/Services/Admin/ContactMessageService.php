<?php

namespace App\Services\Admin;

use App\Models\ContactMessage;
use App\Repositories\Contracts\ContactMessageRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactMessageService
{
    public function __construct(protected ContactMessageRepositoryInterface $contactMessages) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->contactMessages->paginate($filters, 15);
    }

    public function markAsRead(ContactMessage $contactMessage): void
    {
        $this->contactMessages->update($contactMessage, ['is_read' => true]);
    }

    public function delete(ContactMessage $contactMessage): void
    {
        $this->contactMessages->delete($contactMessage);
    }
}
