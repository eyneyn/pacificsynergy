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
        Schema::create('production_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_reports_id')->constrained();
            $table->foreignId('maintenances_id')->constrained('maintenances');
            $table->text('remarks')->nullable();
            $table->integer('minutes')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_issues');
    }
};
