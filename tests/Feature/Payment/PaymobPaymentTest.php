<?php

namespace Tests\Feature\Payment;

use App\Models\Address;
use App\Models\Customer;
use App\Models\ProductVariant;
use App\Models\ShippingZone;
use App\Services\Admin\SettingService;
use App\Services\Storefront\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PaymobPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Permission::firstOrCreate(['name' => 'orders.manage', 'guard_name' => 'admin']);
    }

    protected function configurePaymob(): void
    {
        app(SettingService::class)->updateGroup('payment_paymob', [
            'mode' => 'test',
            'api_key' => 'test-api-key',
            'public_key' => 'test-public-key',
            'integration_id' => '111',
            'iframe_id' => '999',
            'hmac_secret' => 'test-hmac-secret',
        ]);
    }

    protected function buildOrderForCheckout(Customer $customer): Address
    {
        $address = Address::factory()->for($customer)->create(['governorate' => 'القاهرة']);
        ShippingZone::factory()->create(['governorate' => 'القاهرة', 'cost' => 40, 'is_active' => true]);
        $variant = ProductVariant::factory()->create(['stock_quantity' => 10, 'price' => 500]);

        $cart = app(CartService::class)->currentCart($customer->id, null);
        app(CartService::class)->addItem($cart, $variant, 1);

        return $address;
    }

    public function test_checkout_with_paymob_redirects_to_the_generated_iframe_url(): void
    {
        Notification::fake();
        $this->configurePaymob();

        Http::fake([
            'https://accept.paymob.com/api/auth/tokens' => Http::response(['token' => 'auth-token-abc']),
            'https://accept.paymob.com/api/ecommerce/orders' => Http::response(['id' => 55555]),
            'https://accept.paymob.com/api/acceptance/payment_keys' => Http::response(['token' => 'payment-token-xyz']),
        ]);

        $customer = Customer::factory()->create();
        $address = $this->buildOrderForCheckout($customer);

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'paymob',
        ]);

        $response->assertRedirect('https://accept.paymob.com/api/acceptance/iframes/999?payment_token=payment-token-xyz');
        $this->assertDatabaseHas('orders', ['customer_id' => $customer->id, 'payment_method' => 'paymob', 'payment_status' => 'pending']);
    }

    public function test_checkout_with_paymob_fails_gracefully_when_gateway_is_not_configured(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $address = $this->buildOrderForCheckout($customer);

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.checkout.store'), [
            'address_id' => $address->id,
            'payment_method' => 'paymob',
        ]);

        $response->assertSessionHasErrors('payment');
    }

    protected function buildWebhookPayload(string $merchantOrderId, bool $success, string $hmacSecret): array
    {
        $obj = [
            'amount_cents' => 50000,
            'created_at' => '2026-01-01T00:00:00.000000',
            'currency' => 'EGP',
            'error_occured' => false,
            'has_parent_transaction' => false,
            'id' => 987654,
            'integration_id' => 111,
            'is_3d_secure' => false,
            'is_auth' => false,
            'is_capture' => false,
            'is_refunded' => false,
            'is_standalone_payment' => true,
            'is_voided' => false,
            'order' => ['id' => 55555, 'merchant_order_id' => $merchantOrderId],
            'owner' => 222,
            'pending' => false,
            'source_data' => ['pan' => '1234', 'sub_type' => 'MasterCard', 'type' => 'card'],
            'success' => $success,
        ];

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

        $hmac = hash_hmac('sha512', $concatenated, $hmacSecret);

        return [$obj, $hmac];
    }

    public function test_webhook_with_valid_signature_marks_order_as_paid(): void
    {
        Notification::fake();
        $this->configurePaymob();

        $customer = Customer::factory()->create();
        $address = $this->buildOrderForCheckout($customer);
        $order = app(\App\Services\OrderService::class)->placeOrder(
            $customer,
            app(CartService::class)->currentCart($customer->id, null)->fresh('items.productVariant.product'),
            $address,
            'paymob',
            null,
            null,
        );

        [$obj, $hmac] = $this->buildWebhookPayload($order->order_number, true, 'test-hmac-secret');

        $response = $this->postJson(route('payment.paymob.webhook', ['hmac' => $hmac]), ['obj' => $obj]);

        $response->assertOk();
        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertDatabaseHas('payments', ['order_id' => $order->id, 'gateway' => 'paymob']);
    }

    public function test_webhook_with_invalid_signature_is_rejected(): void
    {
        Notification::fake();
        $this->configurePaymob();

        $customer = Customer::factory()->create();
        $address = $this->buildOrderForCheckout($customer);
        $order = app(\App\Services\OrderService::class)->placeOrder(
            $customer,
            app(CartService::class)->currentCart($customer->id, null)->fresh('items.productVariant.product'),
            $address,
            'paymob',
            null,
            null,
        );

        [$obj] = $this->buildWebhookPayload($order->order_number, true, 'test-hmac-secret');

        $response = $this->postJson(route('payment.paymob.webhook', ['hmac' => 'not-the-real-signature']), ['obj' => $obj]);

        $response->assertStatus(401);
        $this->assertSame('pending', $order->fresh()->payment_status);
    }

    public function test_return_url_redirects_to_order_page_with_success_message(): void
    {
        $response = $this->get(route('payment.paymob.return', ['merchant_order_id' => 'ATHR-20260101-TEST', 'success' => 'true']));

        $response->assertRedirect(route('storefront.orders.show', 'ATHR-20260101-TEST'));
        $response->assertSessionHas('success');
    }

    public function test_return_url_redirects_with_error_message_on_failure(): void
    {
        $response = $this->get(route('payment.paymob.return', ['merchant_order_id' => 'ATHR-20260101-TEST', 'success' => 'false']));

        $response->assertSessionHas('error');
    }
}
