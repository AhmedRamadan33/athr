<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\Admin\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(protected CustomerService $customerService) {}

    public function index(Request $request): View
    {
        return view('admin.customers.index', [
            'customers' => $this->customerService->paginate($request->only(['search', 'status'])),
        ]);
    }

    public function show(Customer $customer): View
    {
        return view('admin.customers.show', [
            'customer' => $customer->load(['addresses', 'orders' => fn ($q) => $q->latest(), 'reviews.product']),
        ]);
    }

    public function toggleActive(Customer $customer): RedirectResponse
    {
        $this->customerService->toggleActive($customer);

        return back()->with('success', 'تم تحديث حالة العميل بنجاح.');
    }
}
