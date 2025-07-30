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
    Schema::create('production_report_histories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('production_report_id')->constrained()->onDelete('cascade');
        $table->json('old_data');
        $table->json('new_data');
        $table->foreignId('updated_by')->constrained('users')->onDelete('cascade');
        $table->timestamp('updated_at');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_report_histories');
    }
};
