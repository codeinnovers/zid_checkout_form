<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zid_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zid_merchant_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->index();
            $table->json('form_data')->nullable();
            $table->json('attachments')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zid_form_submissions');
    }
};
