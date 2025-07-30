<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration for creating the 'standards' table
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('standards', function (Blueprint $table) {
            $table->id();
            $table->string('group');
            $table->string('mat_no');
            $table->string('size');
            $table->string('description')->unique();
            $table->integer('bottles_per_case');
            $table->string('preform_weight');
            $table->string('ldpe_size');
            $table->integer('cases_per_roll');
            $table->string('caps');
            $table->string('opp_label');
            $table->string('barcode_sticker');
            $table->decimal('alt_preform_for_350ml', 8, 3);
            $table->decimal('preform_weight2', 8, 3);
            $table->timestamps();
            $table->softDeletes(); // 👈 adds deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standards');
    }
};
