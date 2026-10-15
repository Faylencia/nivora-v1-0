<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inventories', 'business_id')) {
            Schema::table('inventories', function (Blueprint $table): void {
                $table->foreignId('business_id')->nullable()->after('product_id')->constrained('businesses')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('inventories', 'outlet_id')) {
            foreach (DB::table('inventories')->select('inventories.id', 'products.business_id')
                ->join('products', 'products.id', '=', 'inventories.product_id')->orderBy('inventories.id')->get() as $inventory) {
                DB::table('inventories')->where('id', $inventory->id)->update(['business_id' => $inventory->business_id]);
            }

            Schema::table('inventories', function (Blueprint $table): void {
                $table->index('product_id', 'inventories_product_id_merge_support');
                $table->dropUnique('inventories_product_outlet_unique');
                $table->dropColumn('outlet_id');
                $table->unique(['product_id', 'business_id'], 'inventories_product_business_unique');
                $table->dropIndex('inventories_product_id_merge_support');
                $table->unsignedBigInteger('business_id')->nullable(false)->change();
            });
        } elseif (collect(Schema::getIndexes('inventories'))->contains('name', 'inventories_product_id_merge_support')) {
            Schema::table('inventories', function (Blueprint $table): void {
                $table->dropIndex('inventories_product_id_merge_support');
            });
        }

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->index('user_id', 'user_roles_user_id_merge_support');
            $table->dropUnique('user_roles_unique_scope');
            $table->dropColumn('outlet_id');
            $table->unique(['user_id', 'role_id', 'business_id'], 'user_roles_business_scope_unique');
        });

        foreach (['sales', 'ai_recommendations', 'store_analyses'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('outlet_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('user_roles', function (Blueprint $table): void {
            $table->dropUnique('user_roles_business_scope_unique');
            $table->foreignId('outlet_id')->nullable()->after('business_id');
        });
        DB::table('user_roles')->whereNotNull('business_id')->update(['outlet_id' => DB::raw('business_id')]);
        Schema::table('user_roles', function (Blueprint $table): void {
            $table->unique(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_unique_scope');
            $table->index(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_nullable_scope_support');
        });

        foreach (['sales', 'ai_recommendations', 'store_analyses'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('outlet_id')->nullable()->after('business_id');
            });
            DB::table($tableName)->whereNotNull('business_id')->update(['outlet_id' => DB::raw('business_id')]);

            if (in_array($tableName, ['sales', 'store_analyses'], true)) {
                Schema::table($tableName, function (Blueprint $table): void {
                    $table->unsignedBigInteger('outlet_id')->nullable(false)->change();
                });
            }
        }

        Schema::table('inventories', function (Blueprint $table): void {
            $table->foreignId('outlet_id')->nullable()->after('product_id');
        });
        DB::table('inventories')->update(['outlet_id' => DB::raw('business_id')]);
        Schema::table('inventories', function (Blueprint $table): void {
            $table->index('product_id', 'inventories_product_id_restore_support');
            $table->dropUnique('inventories_product_business_unique');
            $table->dropConstrainedForeignId('business_id');
            $table->unsignedBigInteger('outlet_id')->nullable(false)->change();
            $table->unique(['product_id', 'outlet_id'], 'inventories_product_outlet_unique');
            $table->dropIndex('inventories_product_id_restore_support');
        });
    }
};
