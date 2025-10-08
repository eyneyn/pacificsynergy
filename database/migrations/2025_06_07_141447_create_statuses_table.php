<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
                
            $table->foreignId('production_report_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('status', ['Submitted', 'Reviewed', 'Validated', 'Voided']);

            // 👉 add this for remarks when void
            $table->string('remarks')->nullable();

            // Single timestamp for when status happened
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::table('statuses', function (Blueprint $table) {
            $table->dropColumn('remarks');
        });
    }
};
