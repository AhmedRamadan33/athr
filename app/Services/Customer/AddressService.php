<?php

namespace App\Services\Customer;

use App\Models\Address;
use App\Models\Customer;
use App\Repositories\Contracts\AddressRepositoryInterface;
use Illuminate\Validation\ValidationException;

class AddressService
{
    public function __construct(protected AddressRepositoryInterface $addresses) {}

    public function create(Customer $customer, array $data): Address
    {
        if ($data['is_default'] ?? false) {
            $customer->addresses()->update(['is_default' => false]);
        }

        return $customer->addresses()->create([
            'label' => $data['label'] ?? 'المنزل',
            'governorate' => $data['governorate'],
            'city' => $data['city'],
            'address_line' => $data['address_line'],
            'phone' => $data['phone'],
            'is_default' => $data['is_default'] ?? $customer->addresses()->doesntExist(),
        ]);
    }

    public function update(Address $address, array $data): Address
    {
        if ($data['is_default'] ?? false) {
            $address->customer->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        return $this->addresses->update($address, [
            'label' => $data['label'] ?? $address->label,
            'governorate' => $data['governorate'],
            'city' => $data['city'],
            'address_line' => $data['address_line'],
            'phone' => $data['phone'],
            'is_default' => $data['is_default'] ?? $address->is_default,
        ]);
    }

    public function delete(Address $address): void
    {
        if ($address->orders()->exists()) {
            throw ValidationException::withMessages([
                'address' => 'لا يمكن حذف عنوان مرتبط بطلبات سابقة.',
            ]);
        }

        $this->addresses->delete($address);
    }
}
