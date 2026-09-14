<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Admin\SettingService;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PaymobService
{
    const BASE_URL = 'https://accept.paymob.com/api';

    public function __construct(protected SettingService $settings) {}

    public function createPaymentIframeUrl(Order $order): string
    {
        $config = $this->settings->getGroup('payment_paymob');

        if (empty($config['api_key']) || empty($config['integration_id']) || empty($config['iframe_id'])) {
            throw ValidationException::withMessages([
                'payment' => 'بوابة الدفع الإلكترونى غير مُفعّلة حاليًا، برجاء اختيار الدفع عند الاستلام أو التواصل مع المتجر.',
            ]);
        }

        $authToken = $this->requestAuthToken($config['api_key']);
        $paymobOrderId = $this->registerOrder($authToken, $order);
        $paymentToken = $this->requestPaymentKey($authToken, $order, $paymobOrderId, $config['integration_id']);

        return self::BASE_URL."/acceptance/iframes/{$config['iframe_id']}?payment_token={$paymentToken}";
    }

    protected function requestAuthToken(string $apiKey): string
    {
        $response = Http::post(self::BASE_URL.'/auth/tokens', ['api_key' => $apiKey]);

        if (! $response->successful()) {
            throw ValidationException::withMessages(['payment' => 'تعذر الاتصال ببوابة الدفع، برجاء المحاولة لاحقًا.']);
        }

        return $response->json('token');
    }

    protected function registerOrder(string $authToken, Order $order): int
    {
        $response = Http::post(self::BASE_URL.'/ecommerce/orders', [
            'auth_token' => $authToken,
            'delivery_needed' => false,
            'amount_cents' => (int) round($order->total * 100),
            'currency' => 'EGP',
            'merchant_order_id' => $order->order_number,
            'items' => [],
        ]);

        if (! $response->successful()) {
            throw ValidationException::withMessages(['payment' => 'تعذر إنشاء طلب الدفع، برجاء المحاولة لاحقًا.']);
        }

        return $response->json('id');
    }

    protected function requestPaymentKey(string $authToken, Order $order, int $paymobOrderId, string $integrationId): string
    {
        $address = $order->address;

        $response = Http::post(self::BASE_URL.'/acceptance/payment_keys', [
            'auth_token' => $authToken,
            'amount_cents' => (int) round($order->total * 100),
            'expiration' => 3600,
            'order_id' => $paymobOrderId,
            'currency' => 'EGP',
            'integration_id' => (int) $integrationId,
            'billing_data' => [
                'first_name' => $order->customer->name,
                'last_name' => 'NA',
                'email' => $order->customer->email,
                'phone_number' => $address->phone,
                'city' => $address->city,
                'country' => 'EG',
                'street' => $address->address_line,
                'building' => 'NA',
                'floor' => 'NA',
                'apartment' => 'NA',
                'state' => $address->governorate,
            ],
        ]);

        if (! $response->successful()) {
            throw ValidationException::withMessages(['payment' => 'تعذر تجهيز صفحة الدفع، برجاء المحاولة لاحقًا.']);
        }

        return $response->json('token');
    }

    public function verifyHmac(array $payload, string $receivedHmac): bool
    {
        $config = $this->settings->getGroup('payment_paymob');

        if (empty($config['hmac_secret'])) {
            return false;
        }

        $obj = $payload['obj'] ?? $payload;

        $orderedKeys = [
            'amount_cents', 'created_at', 'currency', 'error_occured', 'has_parent_transaction', 'id',
            'integration_id', 'is_3d_secure', 'is_auth', 'is_capture', 'is_refunded', 'is_standalone_payment',
            'is_voided', 'order.id', 'owner', 'pending', 'source_data.pan', 'source_data.sub_type',
            'source_data.type', 'success',
        ];

        $concatenated = collect($orderedKeys)
            ->map(fn (string $key) => data_get($obj, $key))
            ->map(fn ($value) => is_bool($value) ? ($value ? 'true' : 'false') : (string) $value)
            ->implode('');

        $calculatedHmac = hash_hmac('sha512', $concatenated, $config['hmac_secret']);

        return hash_equals($calculatedHmac, $receivedHmac);
    }
}
