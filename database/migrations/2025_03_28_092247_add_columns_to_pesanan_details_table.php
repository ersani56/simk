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
            $table->string('pemotong',25)->nullable(); // Kolom baru untuk nomor telepon
            $table->string('penjahit',25)->nullable(); // Kolom baru untuk nomor telepon
            $table->string('penyablon',25)->nullable(); // Kolom baru untuk alamat
            $table->string('ket')->nullable(); // Kolom baru untuk alamat
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->dropColumn(['no_faktur', 'kode_bjadi', 'ukuran','harga','jumlah','status']);
        });
    }
};
