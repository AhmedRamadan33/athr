<?php

namespace App\Repositories\Contracts;

use App\Models\Cart;

interface CartRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCustomer(int $customerId): ?Cart;

    public function findBySession(string $sessionId): ?Cart;
}
