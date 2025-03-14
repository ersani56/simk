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
        Schema::create('bahanjadis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_bjadi', 8)
            ->unique()
            ->required();
            $table->string('nama_bjadi', 100)
            ->unique()
            ->required();
            $table->enum('kategori', ['Kaos','Trening','Batik','Celana','Lainnya'])
            ->required();
            $table->enum('satuan', ['Pcs','Stel'])->change()
            ->required();
            $table->integer('upah')
            ->nullable();
            $table->string('gambar1');
            $table->string('gambar2');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahanjadis');
    }
};
