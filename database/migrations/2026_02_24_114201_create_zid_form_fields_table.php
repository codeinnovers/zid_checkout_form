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
        Schema::create('zid_form_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zid_merchant_id')->constrained()->cascadeOnDelete();
            $table->string('field_type');
            $table->string('name');
            $table->string('label');
            $table->string('label_ar')->nullable();
            $table->string('placeholder')->nullable();
            $table->string('placeholder_ar')->nullable();
            $table->json('options')->nullable();
            $table->string('default_value')->nullable();
            $table->json('validation_rules')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->string('status')->default('active');
            $table->timestamps();

            $table->unique(['zid_merchant_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zid_form_fields');
    }
};
