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
        Schema::create('pendaftaran', function (Blueprint $table) {
            $table->id();

            $table->string('nim', 9)->nullable();
            $table->foreign('nim')->references('nim')->on('mahasiswa')->nullOnDelete();
            $table->string('status_seleksi')->nullable();

            $table->foreignId('jabatan_1_id')->nullable()->constrained('jabatan')->nullOnDelete();
            $table->foreignId('jabatan_2_id')->nullable()->constrained('jabatan')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
