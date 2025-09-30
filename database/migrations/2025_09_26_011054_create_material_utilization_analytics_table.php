<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('material_utilization_analytics', function (Blueprint $table) {
            $table->id();

            // reference only (no cascade FK)
            $table->unsignedBigInteger('production_report_id')->unique()->index();

            $table->unsignedBigInteger('line'); 

            $table->date('production_date');
            $table->string('sku'); 
            $table->string('bottlePerCase'); 
            $table->decimal('targetMaterialEfficiency', 6, places: 1)->default(0.00);

            $table->integer('total_output')->default(0);

            // Preforms
            $table->string('preformDesc')->default(0);
            $table->integer('preform_fg')->default(0);
            $table->integer('preform_rej')->default(0);
            $table->integer('preform_qa')->default(0);
            $table->decimal('preform_pct', 6, 2)->default(0.00);

            // Caps
            $table->string('capsDesc')->default(0);
            $table->integer('caps_fg')->default(0);
            $table->integer('caps_rej')->default(0);
            $table->integer('caps_qa')->default(0);
            $table->decimal('caps_pct', 6, 2)->default(0.00);

            // Labels
                        $table->string('labelDesc')->default(0);

            $table->integer('label_fg')->default(0);
            $table->integer('label_rej')->default(0);
            $table->integer('label_qa')->default(0);
            $table->decimal('label_pct', 6, 2)->default(0.00);

            // LDPE
                                    $table->string('ldpeDesc')->default(0);

            $table->integer('ldpe_fg')->default(0);
            $table->integer('ldpe_rej')->default(0);
            $table->integer('ldpe_qa')->default(0);
            $table->decimal('ldpe_pct', 6, 2)->default(0.00);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_utilization_analytics');
    }
};
