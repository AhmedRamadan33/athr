<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShippingZoneRequest;
use App\Http\Requests\Admin\UpdateShippingZoneRequest;
use App\Models\ShippingZone;
use App\Services\Admin\ShippingZoneService;
use App\Support\EgyptGovernorates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingZoneController extends Controller
{
    public function __construct(protected ShippingZoneService $shippingZoneService) {}

    public function index(Request $request): View
    {
        return view('admin.shipping-zones.index', [
            'zones' => $this->shippingZoneService->paginate($request->only(['search', 'status'])),
        ]);
    }

    public function create(): View
    {
        return view('admin.shipping-zones.form', [
            'zone' => new ShippingZone,
            'governorates' => EgyptGovernorates::LIST,
        ]);
    }

    public function store(StoreShippingZoneRequest $request): RedirectResponse
    {
        $this->shippingZoneService->create($request->validated());

        return redirect()->route('admin.shipping-zones.index')->with('success', 'تمت إضافة منطقة الشحن بنجاح.');
    }

    public function edit(ShippingZone $shippingZone): View
    {
        return view('admin.shipping-zones.form', [
            'zone' => $shippingZone,
            'governorates' => EgyptGovernorates::LIST,
        ]);
    }

    public function update(UpdateShippingZoneRequest $request, ShippingZone $shippingZone): RedirectResponse
    {
        $this->shippingZoneService->update($shippingZone, $request->validated());

        return redirect()->route('admin.shipping-zones.index')->with('success', 'تم تحديث منطقة الشحن بنجاح.');
    }

    public function destroy(ShippingZone $shippingZone): RedirectResponse
    {
        $this->shippingZoneService->delete($shippingZone);

        return redirect()->route('admin.shipping-zones.index')->with('success', 'تم حذف منطقة الشحن بنجاح.');
    }
}
