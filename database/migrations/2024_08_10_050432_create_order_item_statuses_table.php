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
        Schema::create('order_item_statuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();;
            $table->string('name');
            $table->string('color');
            $table->unsignedInteger('sort_key')->unique();
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('order_item_statuses', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('order_item_statuses');

        Schema::dropIfExists('order_item_statuses');
    }
};
