<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use App\Services\Admin\CouponService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function __construct(protected CouponService $couponService) {}

    public function index(Request $request): View
    {
        return view('admin.coupons.index', [
            'coupons' => $this->couponService->paginate($request->only(['search', 'status'])),
        ]);
    }

    public function create(): View
    {
        return view('admin.coupons.form', ['coupon' => new Coupon]);
    }

    public function store(StoreCouponRequest $request): RedirectResponse
    {
        $this->couponService->create($request->validated());

        return redirect()->route('admin.coupons.index')->with('success', 'تم إنشاء الكوبون بنجاح.');
    }

    public function edit(Coupon $coupon): View
    {
        return view('admin.coupons.form', ['coupon' => $coupon]);
    }

    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $this->couponService->update($coupon, $request->validated());

        return redirect()->route('admin.coupons.index')->with('success', 'تم تحديث الكوبون بنجاح.');
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->couponService->delete($coupon);

        return redirect()->route('admin.coupons.index')->with('success', 'تم حذف الكوبون بنجاح.');
    }
}
