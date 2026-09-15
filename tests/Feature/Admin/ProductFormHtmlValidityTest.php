<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class ProductFormHtmlValidityTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_edit_page_does_not_nest_form_elements_when_product_has_images(): void
    {
        $admin = Admin::factory()->create();
        $admin->givePermissionTo(Permission::firstOrCreate(['name' => 'catalog.manage', 'guard_name' => 'admin']));

        $product = Product::factory()->create();
        $product->images()->create(['path' => 'products/fake-image.png', 'sort_order' => 1]);

        $response = $this->actingAs($admin, 'admin')->get(route('admin.products.edit', $product));

        $response->assertOk();

        $this->assertFormElementsAreNotNested($response->getContent());
    }

    protected function assertFormElementsAreNotNested(string $html): void
    {
        preg_match_all('/<\s*(\/)?\s*form\b/i', $html, $matches, PREG_OFFSET_CAPTURE);

        $depth = 0;

        foreach ($matches[1] as [$closingMarker]) {
            if ($closingMarker === '/') {
                $depth--;
            } else {
                $depth++;
                $this->assertLessThanOrEqual(1, $depth, 'A <form> element is nested inside another <form> element, which is invalid HTML and breaks form/button association in real browsers.');
            }
        }

        $this->assertSame(0, $depth, 'Mismatched <form>/</form> tag count.');
    }
}
