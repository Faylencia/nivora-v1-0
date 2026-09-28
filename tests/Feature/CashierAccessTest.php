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

    public function test_role_dashboards_have_separate_experiences(): void
    {
        $this->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Super Admin')
            ->assertSee('Tambah Toko')
            ->assertSee('Admin Toko')
            ->assertDontSee('Membership')
            ->assertDontSee('POS / Kasir');

        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Admin Toko')
            ->assertSee('Toko Saya')
            ->assertDontSee('Super Admin');
    }

    public function test_management_routes_are_renderable(): void
    {
        foreach ([
            'super-admin.stores',
            'super-admin.stores.create',
            'super-admin.stores.show',
            'super-admin.stores.edit',
            'super-admin.store-admins',
            'super-admin.store-admins.create',
            'super-admin.store-admins.show',
            'super-admin.store-admins.edit',
        ] as $routeName) {
            $parameters = str_contains($routeName, '.show') || str_contains($routeName, '.edit')
                ? ['store' => 1, 'admin' => 1]
                : [];

            $this->get(route($routeName, $parameters))->assertOk();
        }
    }
}
