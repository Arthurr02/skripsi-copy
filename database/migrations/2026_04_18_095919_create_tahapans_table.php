<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('tahapan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_rekrutmen_id')->constrained('periode_rekrutmen')->cascadeOnDelete();
            $table->string('nama_tahapan');
            $table->text('deskripsi_tahapan')->nullable();
            $table->dateTime('waktu_mulai');
            $table->dateTime('waktu_berakhir');
            $table->integer('urutan_tahapan');
            $table->json('lampiran_tahapan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahapans');
    }
};
