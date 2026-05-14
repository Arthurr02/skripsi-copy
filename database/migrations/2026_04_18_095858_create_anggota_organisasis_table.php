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
        Schema::create('anggota_organisasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_rekrutmen_id')->nullable()->constrained('periode_rekrutmen')->nullOnDelete();
            $table->string('nim', 9);
            $table->string('jabatan')->nullable();
            $table->boolean('panitia_rekrutmen')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggota_organisasis');
    }
};
