<?php

use App\Facades\TenantIsolation;
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
        Schema::create('employee_shop', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('shop_id')->constrained();
            $table->foreignUuid('employee_id')->constrained();
            $table->primary(['shop_id', 'employee_id']);
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('employee_shop', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('employee_shop');

        Schema::dropIfExists('employee_shop');
    }
};
