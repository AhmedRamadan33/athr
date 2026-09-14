<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\StoreAddressRequest;
use App\Http\Requests\Customer\UpdateAddressRequest;
use App\Models\Address;
use App\Services\Customer\AddressService;
use App\Support\EgyptGovernorates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(protected AddressService $addressService) {}

    public function index(): View
    {
        return view('storefront.account.addresses', [
            'addresses' => Auth::guard('customer')->user()->addresses,
            'governorates' => EgyptGovernorates::LIST,
        ]);
    }

    public function store(StoreAddressRequest $request): RedirectResponse
    {
        $this->addressService->create(Auth::guard('customer')->user(), $request->validated());

        return back()->with('success', 'تمت إضافة العنوان بنجاح.');
    }

    public function update(UpdateAddressRequest $request, Address $address): RedirectResponse
    {
        $this->addressService->update($address, $request->validated());

        return back()->with('success', 'تم تحديث العنوان بنجاح.');
    }

    public function destroy(Address $address): RedirectResponse
    {
        abort_unless($address->customer_id === Auth::guard('customer')->id(), 403);

        $this->addressService->delete($address);

        return back()->with('success', 'تم حذف العنوان بنجاح.');
    }
}
