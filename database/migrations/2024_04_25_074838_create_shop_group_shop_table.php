<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_group_shop', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('shop_group_id')->constrained();
            $table->foreignUuid('shop_id')->constrained();
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('shop_group_shop', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('shop_group_shop');

        Schema::dropIfExists('shop_group_shop');
    }
};
