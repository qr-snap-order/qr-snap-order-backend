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
        Schema::create('order_item_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('order_item_id')->constrained();
            $table->foreignUuid('order_item_status_id')->constrained();
            $table->string('userable_type');
            $table->uuid('userable_id');
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('order_item_histories', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('order_item_histories');

        Schema::dropIfExists('order_item_histories');
    }
};
