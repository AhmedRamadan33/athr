<?php

namespace App\Services\Admin;

use App\Models\ShippingZone;
use App\Repositories\Contracts\ShippingZoneRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShippingZoneService
{
    public function __construct(protected ShippingZoneRepositoryInterface $shippingZones) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->shippingZones->paginate($filters, 15);
    }

    public function create(array $data): ShippingZone
    {
        return $this->shippingZones->create($data);
    }

    public function update(ShippingZone $zone, array $data): ShippingZone
    {
        return $this->shippingZones->update($zone, $data);
    }

    public function delete(ShippingZone $zone): void
    {
        $this->shippingZones->delete($zone);
    }
}
