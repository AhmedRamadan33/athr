<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\Payment\PaymobService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymobCallbackController extends Controller
{
    public function __construct(
        protected PaymobService $paymobService,
        protected OrderService $orderService,
    ) {}

    public function webhook(Request $request): Response
    {
        $hmac = $request->query('hmac', '');

        if (! $this->paymobService->verifyHmac($request->all(), $hmac)) {
            return response('Invalid signature', 401);
        }

        $obj = $request->input('obj', []);
        $merchantOrderId = $obj['order']['merchant_order_id'] ?? null;
        $order = Order::where('order_number', $merchantOrderId)->first();

        if ($order && $order->payment_status === 'pending') {
            $this->orderService->recordPaymobCallback($order, $obj, (bool) ($obj['success'] ?? false));
        }

        return response('OK', 200);
    }

    public function returnUrl(Request $request): RedirectResponse
    {
        $merchantOrderId = $request->query('merchant_order_id');
        $success = $request->query('success') === 'true';

        return redirect()->route('storefront.orders.show', $merchantOrderId)
            ->with($success ? 'success' : 'error', $success ? 'تم الدفع بنجاح.' : 'لم تتم عملية الدفع، يمكنك المحاولة مرة أخرى.');
    }
}
