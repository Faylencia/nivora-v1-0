<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            return;
        }

        $role = Role::query()->firstOrCreate(
            ['name' => 'super_admin'],
            ['description' => 'Nivora platform administrator.'],
        );
        $user = User::query()->updateOrCreate(
            ['email' => 'superadmin@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => 'superadmin',
                'phone' => null,
                'status' => 'active',
            ],
        );

        $user->roles()->syncWithoutDetaching([
            $role->id => ['business_id' => null],
        ]);
    }
}
