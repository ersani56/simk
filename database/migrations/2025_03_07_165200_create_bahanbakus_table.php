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
        Schema::create('bahanbakus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bbaku')->unique();
            $table->string('nama_bbaku')->unique();
            $table->enum('satuan',['Kg','Mtr','Yrd','Pcs']);
            $table->integer('harga')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahanbakus');
    }
};
