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
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 255);
            $table->string('subdomain', 255);
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('tenants', 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('tenants');

        Schema::dropIfExists('tenants');
    }
};
