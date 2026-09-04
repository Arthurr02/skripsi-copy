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
        Schema::create('keputusan_seleksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahapan_id')->constrained('tahapan')->cascadeOnDelete();
            $table->foreignId('jabatan_id')->constrained('jabatan')->cascadeOnDelete();
            $table->foreignId('pendaftaran_id')->constrained('pendaftaran')->cascadeOnDelete();
            $table->string('keputusan', 20);
            $table->string('aktor_tipe', 20)->nullable();
            $table->unsignedBigInteger('aktor_akun_id')->nullable();
            $table->string('aktor_nama')->nullable();
            $table->timestamp('diputuskan_pada')->nullable();
            $table->timestamps();

            $table->unique(['tahapan_id', 'jabatan_id', 'pendaftaran_id'], 'keputusan_seleksi_unik');
        });

        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->string('pewawancara_tipe', 20)->nullable();
            $table->unsignedBigInteger('pewawancara_akun_id')->nullable();
            $table->string('pewawancara_nama')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengumpulan_tugas', function (Blueprint $table) {
            $table->dropColumn([
                'pewawancara_tipe',
                'pewawancara_akun_id',
                'pewawancara_nama',
            ]);
        });

        Schema::dropIfExists('keputusan_seleksi');
    }
};
