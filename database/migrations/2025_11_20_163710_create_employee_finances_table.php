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
        Schema::create('employee_finances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('gaji_pokok');
            $table->integer('jam_lembur')->default(0);
            $table->integer('gaji_lembur')->default(0);
            $table->integer('tidak_masuk');
            $table->integer('total_gaji');
            $table->date('work_month');
            $table->date('salary_month');
            $table->unique(['user_id', 'work_month']);
            $table->unique(['user_id', 'salary_month']);
            $table->text('keterangan')->nullable();
            $table->enum('status_pegawai', ['magang', 'contract']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_finances');
    }
};
