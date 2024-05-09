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
        Schema::create('menu_item_category', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('menu_item_id')->constrained();
            $table->foreignUuid('category_id')->constrained();
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('menu_item_category', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('menu_item_category');

        Schema::dropIfExists('menu_item_category');
    }
};
