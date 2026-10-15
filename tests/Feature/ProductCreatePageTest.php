<?php

namespace Tests\Feature;

use Tests\TestCase;

class ProductCreatePageTest extends TestCase
{
    public function test_product_creation_page_contains_a_product_and_initial_stock_form(): void
    {
        $this->get(route('owner.products.create'))
            ->assertOk()
            ->assertSee('data-product-create-root', false)
            ->assertSee('name="name"', false)
            ->assertSee('name="category_id"', false)
            ->assertSee('name="outlet_id"', false)
            ->assertSee('name="initial_stock"', false)
            ->assertSee('data-category-form', false);
    }
}
