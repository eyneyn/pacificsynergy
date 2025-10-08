<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // 🔔 Who created the notification (actor)
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // 🔔 Type of notification (e.g. report_create, employee_update, etc.)
            $table->string('type');

            // 🔗 Optional relation to a Production Report
            $table->foreignId('production_report_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');

            // 🔗 Specific user to notify (private notifications)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');

            // 🔗 Employee reference (when notification is about an employee)
            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('users') // since employees are in users table
                ->onDelete('cascade');

            // 🔗 Role reference (Spatie roles table)
            $table->foreignId('role_id')
                ->nullable()
                ->constrained('roles')   // 👈 must reference the roles table
                ->onDelete('cascade');

            // 🔗 Defect reference
            $table->foreignId('defect_id')
                ->nullable()
                ->constrained('defects')
                ->onDelete('cascade');

            // 🔗 Standard reference
            $table->foreignId('standard_id')
                ->nullable()
                ->constrained('standards')
                ->onDelete('cascade');

            // 📝 Notification message (HTML-safe content)
            $table->longText('message');

            // ✅ Mark as read/unread
            $table->boolean('is_read')->default(false);

            // 🔐 Permission-based broadcast (Spatie)
            $table->string('required_permission')->nullable()
                ->comment('Spatie permission required to see this notification');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop foreign keys explicitly before dropping the table
            $table->dropForeign(['created_by']);
            $table->dropForeign(['production_report_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['role_id']);
            $table->dropForeign(['defect_id']);
            $table->dropForeign(['standard_id']);
        });

        Schema::dropIfExists('notifications');
    }
};
