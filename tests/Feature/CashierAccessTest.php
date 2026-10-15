<?php

namespace Tests\Feature;

use App\Models\Business;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashierAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_cashier_dashboard_routes_are_available(): void
    {
        $this->get(route('cashier.dashboard'))
            ->assertOk()
            ->assertSee('POS / Kasir')
            ->assertSee('Riwayat Transaksi')
            ->assertDontSee('Business')
            ->assertDontSee('AI');

        $this->get(route('cashier.pos'))
            ->assertOk()
            ->assertSee('data-go-back', false)
            ->assertSee(route('cashier.dashboard'), false);
        $this->get(route('cashier.sales'))->assertOk();
    }

    public function test_role_dashboards_have_separate_experiences(): void
    {
        $this->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Super Admin')
            ->assertSee('Tinjau pengajuan toko')
            ->assertSee('Admin Toko')
            ->assertDontSee('Membership')
            ->assertDontSee('POS / Kasir');

        $this->get(route('owner.dashboard'))
            ->assertOk()
            ->assertSee('Admin Toko')
            ->assertSee('Toko Saya')
            ->assertSee('space-y-0.5 overflow-y-auto', false)
            ->assertDontSee('scrollbar-hidden', false)
            ->assertDontSee('Super Admin');
    }

    public function test_management_routes_are_renderable(): void
    {
        $business = Business::factory()->create();
        $admin = User::factory()->create();

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
                ? ['store' => $business->id, 'admin' => $admin->id]
                : [];

            $response = $this->get(route($routeName, $parameters));

            if (in_array($routeName, ['super-admin.stores.create', 'super-admin.store-admins.create'], true)) {
                $response->assertRedirect(route('super-admin.business-applications'));

                continue;
            }

            $response->assertOk();
        }
    }
}
