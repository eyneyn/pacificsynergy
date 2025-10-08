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
        Schema::create('defects', function (Blueprint $table) {
            $table->id();
            $table->string('defect_name');
            $table->enum('category', ['Caps', 'Bottle', 'Label', 'LDPE Shrinkfilm']);
            $table->text('description');
            $table->timestamps();
            $table->softDeletes(); // 👈 adds deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('defects');
    }
};
