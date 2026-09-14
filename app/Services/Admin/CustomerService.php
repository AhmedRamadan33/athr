<?php

namespace App\Services\Admin;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerService
{
    public function __construct(protected CustomerRepositoryInterface $customers) {}

    public function paginate(array $filters): LengthAwarePaginator
    {
        return $this->customers->paginate($filters, 15);
    }

    public function toggleActive(Customer $customer): Customer
    {
        return $this->customers->update($customer, ['is_active' => ! $customer->is_active]);
    }
}
