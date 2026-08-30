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
        Schema::create('manage_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nama_lengkap');
            $table->string('email_kantor')->unique();
            $table->string('email_pribadi');
            $table->string('no_hp');
            $table->string('no_keluarga_1');
            $table->string('no_keluarga_2')->nullable();
            $table->enum('jenis_kelamin', ['pria', 'perempuan']);
            $table->text('alamat');

            // Integrasi data SIM / SIO (Lisensi Driver)
            $table->string('nomor_sim')->nullable();
            $table->string('tipe_sim')->nullable(); // SIM A, B1 Umum, B2, SIO
            $table->date('expired_sim')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manage_employees');
    }
};
