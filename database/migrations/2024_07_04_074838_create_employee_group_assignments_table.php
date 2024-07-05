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
        Schema::create('employee_group_assignments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('employee_group_id')->constrained();
            $table->foreignUuid('employee_id')->constrained();
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('employee_group_assignments', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('employee_group_assignments');

        Schema::dropIfExists('employee_group_assignments');
    }
};
