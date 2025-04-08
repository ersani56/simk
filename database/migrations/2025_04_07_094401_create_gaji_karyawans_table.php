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
        Schema::create('gaji_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pesanan_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('karyawan_id')->constrained('users');
            $table->string('peran'); // pemotong / penjahit / penyablon
            $table->integer('jumlah');
            $table->integer('upah');
            $table->integer('total');
            $table->date('tanggal_dibayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gaji_karyawans');
    }
};
