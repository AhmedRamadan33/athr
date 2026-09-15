<?php

namespace Tests\Feature\Storefront;

use App\Models\Admin;
use App\Models\Customer;
use App\Models\Product;
use App\Notifications\NewReviewNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_a_review_and_admins_are_notified(): void
    {
        Notification::fake();

        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'reviews.manage', 'guard_name' => 'admin']));

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.reviews.store', $product), [
            'rating' => 5,
            'comment' => 'عطر رائع جدًا',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('reviews', [
            'customer_id' => $customer->id,
            'product_id' => $product->id,
            'rating' => 5,
            'is_approved' => false,
        ]);
        Notification::assertSentTo($admin, NewReviewNotification::class);
    }

    public function test_customer_cannot_review_the_same_product_twice(): void
    {
        Notification::fake();

        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($customer, 'customer')->post(route('storefront.reviews.store', $product), ['rating' => 4]);
        $response = $this->actingAs($customer, 'customer')->post(route('storefront.reviews.store', $product), ['rating' => 2]);

        $response->assertSessionHasErrors('review');
        $this->assertDatabaseCount('reviews', 1);
    }

    public function test_guest_cannot_submit_a_review(): void
    {
        $product = Product::factory()->create();

        $response = $this->post(route('storefront.reviews.store', $product), ['rating' => 5]);

        $response->assertRedirect(route('storefront.login'));
    }

    public function test_rating_must_be_between_one_and_five(): void
    {
        $customer = Customer::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($customer, 'customer')->post(route('storefront.reviews.store', $product), ['rating' => 9]);

        $response->assertSessionHasErrors('rating');
    }
}
