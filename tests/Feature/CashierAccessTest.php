<?php

namespace Tests\Feature;

use Tests\TestCase;

class CashierAccessTest extends TestCase
{
    public function test_cashier_dashboard_routes_are_available(): void
    {
        $this->get(route('cashier.dashboard'))
            ->assertOk()
            ->assertSee('POS / Kasir')
            ->assertSee('Riwayat Transaksi')
            ->assertDontSee('Business')
            ->assertDontSee('AI');

        $this->get(route('cashier.pos'))->assertOk();
        $this->get(route('cashier.sales'))->assertOk();
    }
}
