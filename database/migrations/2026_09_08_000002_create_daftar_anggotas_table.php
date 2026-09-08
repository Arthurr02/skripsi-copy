<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisasi_id')->constrained('organisasi')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('nama_file_asli');
            $table->date('tanggal_mulai_periode');
            $table->date('tanggal_akhir_periode');
            $table->timestamps();

            $table->index(['organisasi_id', 'created_at']);
            $table->index(['tanggal_mulai_periode', 'tanggal_akhir_periode']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_anggota');
    }
};
