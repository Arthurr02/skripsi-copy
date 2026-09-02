<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('periode_rekrutmen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organisasi_id')->nullable()->constrained('organisasi')->nullOnDelete();
            $table->string('tahun_periode');
            $table->boolean('status_aktif')->default(true);
            $table->string('slogan')->nullable();
            $table->text('deskripsi')->nullable();
            $table->json('lampiran_banner')->nullable();
            $table->json('lampiran_pedoman')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('periode_rekrutmens');
    }
};
