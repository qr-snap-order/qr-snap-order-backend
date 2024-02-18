<?php

use App\Facades\TenantIsolation;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        TenantIsolation::createTenantRoleIfNotExists();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::dropTenantRoleIfExists();
    }
};
