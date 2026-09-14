<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\RegisterRequest;
use App\Services\Customer\CustomerService;
use App\Services\Storefront\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(
        protected CustomerService $customerService,
        protected CartService $cartService,
    ) {}

    public function create(): View
    {
        return view('storefront.auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();

        $customer = $this->customerService->register($request->validated());

        Auth::guard('customer')->login($customer);
        $request->session()->regenerate();

        $this->cartService->mergeGuestIntoCustomer($sessionId, $customer->id);

        return redirect()->route('storefront.home')->with('success', 'مرحبًا بك فى أثر! تم إنشاء حسابك بنجاح.');
    }
}
