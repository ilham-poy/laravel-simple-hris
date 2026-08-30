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
        // Schema::create('resigns', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained()->onDelete('cascade');
        //     $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
        //     $table->text('description');
        //     $table->timestamps();
        // });
        Schema::create('resigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Kekurangan utama: Rentang tanggal & Jenis Izin
            $table->enum('jenis_pengajuan', ['cuti_tahunan', 'sakit', 'izin_khusus']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('total_hari');

            $table->text('alasan');
            $table->string('lampiran_surat')->nullable(); // Surat dokter/SK

            // Status approval
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resigns');
    }
};
