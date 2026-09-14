<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): View
    {
        return view('admin.orders.index', [
            'orders' => $this->orderService->paginate($request->only(['search', 'status', 'payment_method', 'from', 'to'])),
        ]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load(['items', 'customer', 'address', 'statusHistories.changedByAdmin', 'payments', 'coupon']),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): RedirectResponse
    {
        $this->orderService->updateStatus($order, $request->validated()['status'], $request->validated()['note'] ?? null, $request->user('admin'));

        return redirect()->route('admin.orders.show', $order)->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }
}
