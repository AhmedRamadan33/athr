<?php

namespace App\Services\Customer;

use App\Models\Customer;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class CustomerService
{
    public function __construct(protected CustomerRepositoryInterface $customers) {}

    public function register(array $data): Customer
    {
        return $this->customers->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);
    }

    public function updateProfile(Customer $customer, array $data): Customer
    {
        $payload = [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        return $this->customers->update($customer, $payload);
    }
}
