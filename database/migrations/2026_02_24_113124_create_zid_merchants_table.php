<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('zid_merchants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('token')->nullable();
            $table->text('xmanager_token')->nullable();
            $table->text('refresh_token')->nullable();
            $table->timestamp('token_expiration')->nullable();
            $table->string('store_reference')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zid_merchants');
    }
};
