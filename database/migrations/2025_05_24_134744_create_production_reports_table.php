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
        Schema::create('production_reports', function (Blueprint $table) {
            $table->id();

            // Basic info
            $table->date('production_date');
            $table->string('shift');

            // Line (FK from lines.line_number)
            $table->unsignedInteger('line');
            $table->foreign('line')->references('line_number')->on('lines')->onDelete('cascade');

            // AC and Attendance
            $table->integer('ac1')->nullable();
            $table->integer('ac2')->nullable();
            $table->integer('ac3')->nullable();
            $table->integer('ac4')->nullable();
            $table->integer('manpower_present')->nullable();
            $table->integer('manpower_absent')->nullable();

            // SKU (FK from standards.description)
            $table->string('sku');
            $table->foreign('sku')->references('description')->on('standards')->onDelete('cascade');

            // Production figures
            $table->string('fbo_fco')->nullable();
            $table->string('lbo_lco')->nullable();
            $table->integer('total_outputCase')->nullable();

            // Filling line
            $table->integer('filler_speed')->nullable();
            $table->integer('opp_labeler_speed')->nullable();
            $table->integer('opp_labels')->nullable();
            $table->integer('shrinkfilm')->nullable();
            $table->integer('caps_filling')->nullable();
            $table->integer('bottle_filling')->nullable();

            // Blow molding
            $table->integer('blow_molding_output')->nullable();
            $table->integer('speed_blow_molding')->nullable();
            $table->integer('preform_blow_molding')->nullable();
            $table->integer('bottles_blow_molding')->nullable();

            // QA
            $table->text('qa_remarks')->nullable();
            $table->integer('with_label')->nullable();
            $table->integer('without_label')->nullable();
            $table->integer('total_sample')->default(0);

            $table->integer('total_downtime')->default(0);
            $table->string('bottle_code');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_reports');
    }
};
