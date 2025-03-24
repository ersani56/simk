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
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->renameColumn('kode_barang', 'kode_bjadi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->renameColumn('kode_bjadi', 'kode_barang');
        });
    }
};
