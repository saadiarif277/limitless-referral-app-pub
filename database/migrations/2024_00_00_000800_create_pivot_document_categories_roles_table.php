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
        Schema::create('pivot_document_types_roles', function (Blueprint $table) {
            $table->id('document_type_roles_id');
            $table->foreignId('document_type_id');
            $table->foreignId('role_id');
            $table->boolean('can_list')->default(false);
            $table->boolean('can_show')->default(false);
            $table->boolean('can_store')->default(false);
            $table->boolean('can_update')->default(false);
            $table->boolean('can_delete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pivot_document_types_roles');
    }
};
