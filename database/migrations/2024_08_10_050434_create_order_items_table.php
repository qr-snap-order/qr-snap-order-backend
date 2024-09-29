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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('order_id')->constrained();
            $table->foreignUuid('menu_item_id')->constrained();
            $table->foreignUuid('order_item_status_id')->constrained();
            $table->unsignedInteger('count');
            $table->unsignedInteger('price')->comment('注文時の税抜価格。マスター価格は変更されることがあるので、注文時の価格を保持する必要がある。');
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('order_items', 'tenant_id');
    }

    /*
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('order_items');

        Schema::dropIfExists('order_items');
    }
};
