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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->string('name', 255);
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('employees', 'tenant_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('employees');

        Schema::dropIfExists('employees');
    }
};
