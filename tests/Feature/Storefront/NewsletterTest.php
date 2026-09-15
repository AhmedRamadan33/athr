<?php

namespace Tests\Feature\Storefront;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_subscribe_to_the_newsletter(): void
    {
        $response = $this->post(route('storefront.newsletter.store'), ['email' => 'fan@example.com']);

        $response->assertRedirect();
        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'fan@example.com', 'unsubscribed_at' => null]);
    }

    public function test_subscribing_again_after_unsubscribing_reactivates_it(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'fan@example.com', 'unsubscribed_at' => now()]);

        $this->post(route('storefront.newsletter.store'), ['email' => 'fan@example.com']);

        $this->assertNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_can_unsubscribe_using_a_valid_signed_link(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'fan@example.com']);
        $url = URL::signedRoute('storefront.newsletter.unsubscribe', ['subscriber' => $subscriber->id]);

        $response = $this->get($url);

        $response->assertRedirect(route('storefront.home'));
        $this->assertNotNull($subscriber->fresh()->unsubscribed_at);
    }

    public function test_unsubscribe_link_without_a_valid_signature_is_rejected(): void
    {
        $subscriber = NewsletterSubscriber::create(['email' => 'fan@example.com']);

        $response = $this->get(route('storefront.newsletter.unsubscribe', ['subscriber' => $subscriber->id]));

        $response->assertForbidden();
        $this->assertNull($subscriber->fresh()->unsubscribed_at);
    }
}
