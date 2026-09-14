<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request): View
    {
        $orders = Auth::guard('customer')->user()
            ->orders()
            ->when($request->filled('search'), fn ($q) => $q->where('order_number', 'like', '%'.$request->string('search').'%'))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('storefront.account.orders', compact('orders'));
    }

    public function show(string $order_number): View
    {
        $order = $this->findCustomerOrder($order_number);

        return view('storefront.account.order-show', compact('order'));
    }

    public function cancel(string $order_number): RedirectResponse
    {
        $order = $this->findCustomerOrder($order_number);

        $this->orderService->cancelByCustomer($order);

        return back()->with('success', 'تم إلغاء الطلب بنجاح.');
    }

    protected function findCustomerOrder(string $order_number): Order
    {
        $order = Order::where('order_number', $order_number)
            ->where('customer_id', Auth::guard('customer')->id())
            ->with(['items', 'statusHistories', 'address'])
            ->first();

        if (! $order) {
            throw new NotFoundHttpException;
        }

        return $order;
    }
}
