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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Hubungkan dengan tabel jadwal/shift hari itu
            $table->foreignId('employee_schedule_id')->nullable()->constrained('employee_schedules')->onDelete('set null');

            $table->date('tanggal');

            // Jam Absen Aktual (Tap In / Tap Out)
            $table->time('jam_masuk');
            $table->time('jam_keluar')->nullable(); // Aktifkan ini untuk checkout

            $table->enum('status', ['hadir', 'izin', 'sakit', 'telat', 'alpha'])->default('alpha');

            // Gunakan menit (integer) agar mudah dihitung oleh Finance saat Payroll
            $table->integer('durasi_keterlambatan_menit')->default(0);

            // Opsional: Bukti Foto / Koordinat (Fitur umum driver/gudang)
            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();

            $table->text('keterangan')->nullable();
            $table->string('lampiran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
