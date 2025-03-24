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
        Schema::create('pesanan_details', function (Blueprint $table) {
            $table->id();
            $table->string('no_faktur',12);
            $table->string('kode_barang',8);
            $table->string('ukuran',10);
            $table->integer('harga');
            $table->integer('jumlah') ;
            $table->string('status',15);
            $table->timestamps();

            $table->foreign('no_faktur')->references('no_faktur')->on('pesanans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pesanan_details');
    }
};
