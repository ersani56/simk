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
        Schema::create('stoks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_stok', 8)
            ->unique()->required();
            $table->string('kode_bbaku', 8)
            ->required();
            $table->integer('jml_stok')
            ->required();
            $table->enum('lokasi',['Rumah','Ruko','Sri Agung','SOhari', 'BUde Imah','Mb Hani'])
            ->required();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stoks');
    }
};
