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
        Schema::create('employee_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal');

            // Detail Shift
            $table->enum('shift_type', ['pagi', 'siang', 'malam', 'off'])->default('pagi');
            $table->time('jam_masuk')->nullable();  // Misal: 08:00:00
            $table->time('jam_keluar')->nullable(); // Misal: 17:00:00

            // Detail Lembur
            $table->decimal('total_lembur', 4, 2)->default(0.00); // Dalam satuan jam (misal 2.50)
            $table->text('keterangan_lembur')->nullable();

            // Status Approval Lembur / Jadwal oleh HRD
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');

            $table->timestamps();

            // Memastikan 1 user hanya punya 1 baris jadwal di tanggal yang sama
            $table->unique(['user_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_employees');
    }
};
