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
        Schema::create('referral_types', function (Blueprint $table) {
            $table->id('referral_type_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_generated')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('referrals', function (Blueprint $table) {
            $table->foreignId('referral_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referral_types');

        Schema::table('referrals', function (Blueprint $table) {
            $table->dropColumn('referral_type_id');
        });
    }
};
