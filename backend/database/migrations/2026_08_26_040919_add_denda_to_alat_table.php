<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->integer('denda_ringan')->default(0)->after('gambar');
            $table->integer('denda_berat')->default(0)->after('denda_ringan');
        });
    }

    public function down(): void
    {
        Schema::table('alat', function (Blueprint $table) {
            $table->dropColumn([
                'denda_ringan',
                'denda_berat',
            ]);
        });
    }
};