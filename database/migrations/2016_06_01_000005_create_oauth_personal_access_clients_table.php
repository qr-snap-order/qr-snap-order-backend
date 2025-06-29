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
        Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('client_id');
            $table->timestamps();
        });

        TenantIsolation::grantAllPrivileges('oauth_clients');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        TenantIsolation::revokeAllPrivileges('oauth_clients');

        Schema::dropIfExists('oauth_personal_access_clients');
    }
};
