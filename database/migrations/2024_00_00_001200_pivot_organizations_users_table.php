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
        Schema::create('pivot_organizations_users', function (Blueprint $table) {
            $table->id('pivot_organizations_users_id');
            $table->foreignId('organization_id');
            $table->foreignId('user_id');
            $table->foreignId('organization_role_id')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_organizations_users');
    }
};
