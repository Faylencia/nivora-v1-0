<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table): void {
            $table->string('location_name', 150)->nullable()->after('name');
        });

        foreach (DB::table('outlets')->orderBy('id')->get() as $outlet) {
            $business = DB::table('businesses')->where('id', $outlet->business_id)->first();

            if ($business) {
                DB::table('businesses')->where('id', $outlet->business_id)->update([
                    'location_name' => $outlet->name,
                    'address' => $business->address ?: $outlet->address,
                    'phone' => $business->phone ?: $outlet->phone,
                ]);
            }
        }

        foreach (['user_roles', 'inventories', 'sales', 'ai_recommendations', 'store_analyses'] as $tableName) {
            if (Schema::hasColumn($tableName, 'outlet_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $table->dropForeign($tableName.'_outlet_id_foreign');
                });
            }
        }

        foreach (['inventories', 'sales', 'ai_recommendations', 'store_analyses'] as $tableName) {
            if (Schema::hasColumn($tableName, 'business_id') && Schema::hasColumn($tableName, 'outlet_id')) {
                DB::table($tableName)->whereNotNull('outlet_id')->update([
                    'outlet_id' => DB::raw('business_id'),
                ]);
            }
        }

        DB::table('user_roles')->whereNotNull('outlet_id')->update([
            'outlet_id' => DB::raw('business_id'),
        ]);

        Schema::drop('outlets');
    }

    public function down(): void
    {
        Schema::create('outlets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('name', 150);
            $table->text('address')->nullable();
            $table->string('phone', 30)->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('created_at')->useCurrent();
            $table->unique('business_id', 'outlets_one_per_business_unique');
        });

        foreach (DB::table('businesses')->orderBy('id')->get() as $business) {
            DB::table('outlets')->insert([
                'id' => $business->id,
                'business_id' => $business->id,
                'name' => $business->location_name ?: $business->name,
                'address' => $business->address,
                'phone' => $business->phone,
                'status' => $business->status,
                'created_at' => now(),
            ]);
        }

        foreach (['user_roles', 'inventories', 'sales', 'ai_recommendations', 'store_analyses'] as $tableName) {
            if (Schema::hasColumn($tableName, 'outlet_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                    $foreignKey = $table->foreign('outlet_id', $tableName.'_outlet_id_foreign')->references('id')->on('outlets');

                    if (in_array($tableName, ['user_roles', 'ai_recommendations'], true)) {
                        $foreignKey->nullOnDelete();
                    } elseif ($tableName === 'inventories') {
                        $foreignKey->cascadeOnDelete();
                    } else {
                        $foreignKey->restrictOnDelete();
                    }
                });
            }
        }

        Schema::table('businesses', function (Blueprint $table): void {
            $table->dropColumn('location_name');
        });
    }
};
