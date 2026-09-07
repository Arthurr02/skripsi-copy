<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tahapan', function (Blueprint $table): void {
            $table->string('jenis_tahapan', 20)
                ->default('seleksi')
                ->after('periode_rekrutmen_id');
        });

        // Tahap tunggal selain tahap pertama sebelumnya ditampilkan aplikasi
        // sebagai pengumuman. Tandai data lama itu secara eksplisit agar tidak
        // keliru memperoleh form/penugasan setelah pembaruan ini.
        DB::table('tahapan')
            ->where('urutan_tahapan', '>', 1)
            ->whereColumn('waktu_mulai', 'waktu_berakhir')
            ->update(['jenis_tahapan' => 'pengumuman']);
    }

    public function down(): void
    {
        Schema::table('tahapan', function (Blueprint $table): void {
            $table->dropColumn('jenis_tahapan');
        });
    }
};
