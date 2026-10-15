<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            [
                'name' => 'super_admin',
                'description' => 'Nivora platform administrator.',
            ],
            [
                'name' => 'owner',
                'description' => 'Business owner or store administrator.',
            ],
            [
                'name' => 'cashier',
                'description' => 'Point-of-sale cashier.',
            ],
            [
                'name' => 'member',
                'description' => 'Nivora member account.',
            ],
        ], ['name'], ['description']);
    }
}
