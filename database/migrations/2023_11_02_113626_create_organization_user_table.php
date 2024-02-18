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
        Schema::create('organization_user', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('organization_id')->default(TenantIsolation::sessionTenantIdExpression())->constrained();
            $table->foreignUuid('user_id')->constrained();
            $table->unique(['organization_id', 'user_id']);
            $table->timestamps();
        });

        TenantIsolation::grantIsolationRowAccessToTenantRole('organization_user', 'organization_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeIsolationRowAccessFromTenantRole('organization_user');

        Schema::dropIfExists('organization_user');
    }
};
