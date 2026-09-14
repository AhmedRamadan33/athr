<?php

namespace App\Repositories\Contracts;

use App\Models\ShippingZone;

interface ShippingZoneRepositoryInterface extends BaseRepositoryInterface
{
    public function findByGovernorate(string $governorate): ?ShippingZone;
}
