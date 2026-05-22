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
        Schema::create('authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('responsible_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('secretary_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('portaria_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 20)->index();
            $table->string('reason')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 40)->default('aguardando_professor')->index();
            $table->timestamp('requested_at')->useCurrent()->index();
            $table->timestamp('teacher_acknowledged_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorizations');
    }
};
