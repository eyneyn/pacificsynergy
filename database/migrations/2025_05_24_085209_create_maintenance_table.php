<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->enum('type', ['EPL', 'OPL']);
            $table->timestamps();
            $table->softDeletes(); // 👈 adds deleted_at column
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances'); // 👈 just drop table
    }
};
