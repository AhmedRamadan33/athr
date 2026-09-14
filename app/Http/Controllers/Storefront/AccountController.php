<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateProfileRequest;
use App\Services\Customer\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(protected CustomerService $customerService) {}

    public function edit(): View
    {
        return view('storefront.account.profile');
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->customerService->updateProfile(Auth::guard('customer')->user(), $request->validated());

        return back()->with('success', 'تم تحديث بياناتك بنجاح.');
    }
}
