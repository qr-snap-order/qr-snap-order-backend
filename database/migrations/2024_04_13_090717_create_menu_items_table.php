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
        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('menu_section_id')->constrained();
            $table->string('name', 255);
            $table->unsignedInteger('price');
            $table->string('image', 255)->nullable();
            $table->unsignedInteger('sort_key');
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('menu_items', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('menu_items');

        Schema::dropIfExists('menu_items');
    }
};
