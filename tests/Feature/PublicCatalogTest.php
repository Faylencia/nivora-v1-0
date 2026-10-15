<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_does_not_render_demo_businesses_or_products(): void
    {
        $this->get(route('explore'))
            ->assertOk()
            ->assertSee('Belum ada bisnis yang cocok.')
            ->assertDontSee('Kopi Tuku')
            ->assertDontSee('Nusa Craft');
    }

    public function test_public_pages_include_member_navigation_and_authenticated_home_actions(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-public-layout', false)
            ->assertSee('data-member-public-sidebar', false)
            ->assertSee('data-auth-guest', false)
            ->assertSee('data-authenticated-nav', false)
            ->assertSee('data-home-authenticated-callout', false);

        $this->get(route('explore'))
            ->assertOk()
            ->assertSee('data-member-public-sidebar', false)
            ->assertSee('Jelajahi Toko');
    }

    public function test_public_catalog_shows_real_records_but_gates_private_details(): void
    {
        $business = Business::factory()->create(['name' => 'Bisnis Terdaftar']);
        $category = Category::factory()->create([
            'business_id' => $business->id,
            'name' => 'Minuman Aktual',
        ]);
        $product = Product::factory()->create([
            'business_id' => $business->id,
            'category_id' => $category->id,
            'name' => 'Produk Aktual',
            'selling_price' => 12000,
        ]);

        $this->get(route('explore'))
            ->assertOk()
            ->assertSee('Bisnis Terdaftar')
            ->assertSee('1 produk aktif');

        $this->get(route('business.show', ['business' => $business->id]))
            ->assertOk()
            ->assertSee('Detail toko khusus member')
            ->assertDontSee('Produk Aktual')
            ->assertDontSee('Minuman Aktual');

        $this->get(route('product.show', ['product' => $product->id]))
            ->assertOk()
            ->assertSee('Detail produk khusus member')
            ->assertDontSee('Produk Aktual')
            ->assertDontSee('Bisnis Terdaftar')
            ->assertDontSee('Kopi Susu Signature');
    }
}
