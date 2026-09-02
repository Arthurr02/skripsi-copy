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
        Schema::table('organisasi', function (Blueprint $table) {
            // Menambahkan kolom avatar_google setelah kolom lampiran_logo
            $table->string('avatar_google')->nullable()->after('lampiran_logo');
        });
    }

    public function down(): void
    {
        Schema::table('organisasi', function (Blueprint $table) {
            $table->dropColumn('avatar_google');
        });
    }
};
