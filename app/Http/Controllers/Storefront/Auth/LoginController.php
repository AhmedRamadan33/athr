<?php

namespace App\Http\Controllers\Storefront\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\Auth\LoginRequest;
use App\Services\Storefront\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(protected CartService $cartService) {}

    public function create(): View
    {
        return view('storefront.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $sessionId = $request->session()->getId();

        $request->authenticate();
        $request->session()->regenerate();

        $this->cartService->mergeGuestIntoCustomer($sessionId, Auth::guard('customer')->id());

        $intended = $request->session()->pull('url.intended');
        $redirectTo = $intended && ! str_starts_with(parse_url($intended, PHP_URL_PATH) ?? '', '/admin')
            ? $intended
            : route('storefront.home');

        return redirect()->to($redirectTo)->with('success', 'تم تسجيل الدخول بنجاح.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->regenerate();

        return redirect()->route('storefront.home');
    }
}
