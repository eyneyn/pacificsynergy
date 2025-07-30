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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // recipient
            $table->string('type'); // e.g. 'status', 'change_log', 'submission'
            $table->foreignId('production_report_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('message'); // optional custom message
            $table->boolean('is_read')->default(false);
            $table->timestamps(); // created_at = when notification was sent
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
