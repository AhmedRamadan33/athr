<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Models\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function create(string $token): View
    {
        return view('admin.auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function store(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::broker('admins')->reset(
            $request->validated(),
            function (Admin $admin, string $password) {
                $admin->forceFill(['password' => Hash::make($password), 'remember_token' => Str::random(60)])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('admin.login')->with('success', 'تم إعادة تعيين كلمة المرور بنجاح، يمكنك تسجيل الدخول الآن.')
            : back()->withErrors(['email' => 'رابط إعادة التعيين غير صالح أو منتهى الصلاحية.']);
    }
}
