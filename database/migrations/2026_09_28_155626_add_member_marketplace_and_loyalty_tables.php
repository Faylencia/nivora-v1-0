<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_roles', function (Blueprint $table): void {
            $table->index(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_nullable_scope_support');
        });

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->dropUnique('user_roles_unique_scope');
        });

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->foreignId('business_id')->nullable()->change();
            $table->unique(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_unique_scope');
            $table->foreign('business_id')->references('id')->on('businesses')->cascadeOnDelete();
            $table->dropIndex('user_roles_nullable_scope_support');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->foreignId('member_user_id')->nullable()->after('business_id')->constrained('users')->nullOnDelete();
            $table->unique(['business_id', 'member_user_id'], 'customers_business_member_unique');
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->foreignId('member_user_id')->nullable()->after('customer_id')->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('points_to_member')->default(0)->after('change_amount');
            $table->index(['member_user_id', 'transaction_date'], 'sales_member_transaction_index');
        });

        Schema::table('promos', function (Blueprint $table): void {
            $table->string('code', 32)->nullable()->after('name');
            $table->unique(['business_id', 'code'], 'promos_business_code_unique');
        });

        Schema::create('business_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('applicant_id')->constrained('users')->cascadeOnDelete();
            $table->string('business_name', 150);
            $table->string('business_type', 100);
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('outlet_name', 150)->nullable();
            $table->text('outlet_address')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->text('review_note')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('point_wallets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->cascadeOnDelete();
            $table->enum('wallet_type', ['member', 'cashier']);
            $table->string('wallet_key', 100)->unique();
            $table->unsignedBigInteger('balance')->default(0);
            $table->timestamps();
            $table->index(['business_id', 'wallet_type']);
        });

        Schema::create('point_transactions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->foreignId('from_wallet_id')->nullable()->constrained('point_wallets')->nullOnDelete();
            $table->foreignId('to_wallet_id')->nullable()->constrained('point_wallets')->nullOnDelete();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->string('type', 30);
            $table->unsignedBigInteger('points');
            $table->unsignedBigInteger('from_balance_after')->nullable();
            $table->unsignedBigInteger('to_balance_after')->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['business_id', 'created_at']);
        });

        Schema::create('member_coupons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->string('code', 32)->unique();
            $table->unsignedInteger('milestone')->default(0);
            $table->decimal('discount_value', 5, 2)->default(10);
            $table->decimal('min_transaction', 15, 2)->default(100000);
            $table->enum('status', ['active', 'redeemed', 'expired'])->default('active');
            $table->dateTime('expires_at');
            $table->foreignId('earned_at_sale_id')->nullable()->constrained('sales')->nullOnDelete();
            $table->foreignId('redeemed_sale_id')->nullable()->unique()->constrained('sales')->nullOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'business_id', 'milestone'], 'member_coupons_milestone_unique');
            $table->index(['user_id', 'status', 'expires_at']);
        });

        Schema::create('member_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('business_id')->nullable()->constrained('businesses')->nullOnDelete();
            $table->string('type', 40);
            $table->string('title', 150);
            $table->text('message');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'read_at', 'created_at'], 'member_notifications_inbox_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_notifications');
        Schema::dropIfExists('member_coupons');
        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('point_wallets');
        Schema::dropIfExists('business_applications');

        Schema::table('promos', function (Blueprint $table): void {
            $table->dropUnique('promos_business_code_unique');
            $table->dropColumn('code');
        });

        Schema::table('sales', function (Blueprint $table): void {
            $table->dropIndex('sales_member_transaction_index');
            $table->dropConstrainedForeignId('member_user_id');
            $table->dropColumn('points_to_member');
        });

        Schema::table('customers', function (Blueprint $table): void {
            $table->dropUnique('customers_business_member_unique');
            $table->dropConstrainedForeignId('member_user_id');
        });

        DB::table('user_roles')->whereNull('business_id')->delete();

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->index(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_nullable_scope_support');
        });

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->dropUnique('user_roles_unique_scope');
        });

        Schema::table('user_roles', function (Blueprint $table): void {
            $table->foreignId('business_id')->nullable(false)->change();
            $table->unique(['user_id', 'role_id', 'business_id', 'outlet_id'], 'user_roles_unique_scope');
            $table->foreign('business_id')->references('id')->on('businesses')->cascadeOnDelete();
            $table->dropIndex('user_roles_nullable_scope_support');
        });
    }
};
