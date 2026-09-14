<?php

namespace App\Services\Admin;

use App\Models\Coupon;
use App\Repositories\Contracts\CouponRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CouponService
{
    public function __construct(protected CouponRepositoryInterface $coupons) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->coupons->paginate($filters, 15);
    }

    public function create(array $data): Coupon
    {
        $data['code'] = strtoupper($data['code']);

        return $this->coupons->create($data);
    }

    public function update(Coupon $coupon, array $data): Coupon
    {
        $data['code'] = strtoupper($data['code']);

        return $this->coupons->update($coupon, $data);
    }

    public function delete(Coupon $coupon): void
    {
        $this->coupons->delete($coupon);
    }
}
