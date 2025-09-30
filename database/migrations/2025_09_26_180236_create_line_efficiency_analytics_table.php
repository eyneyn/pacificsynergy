<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::create('line_efficiency_analytics', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('production_report_id')->index();
    $table->unsignedBigInteger('line'); 
    $table->date('production_date');
    $table->string('sku'); 
    $table->string('bottlesPerCase'); 
    $table->decimal('line_efficiency', 5, 2)->nullable();
    $table->enum('downtime_type', ['EPL', 'OPL']);
    $table->string('category');
    $table->integer('minutes')->default(0);
    $table->timestamps();

    $table->unique(
    ['production_report_id', 'downtime_type', 'category'],
    'lea_report_downtime_cat_unique' // 👈 short name
);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('line_efficiency_analytics');
    }
};
