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
        Schema::table('bahanjadis', function (Blueprint $table) {
            $table->integer('harga')->after('satuan')->nullable();
            $table->integer('upah_potong')->after('harga')->nullable();
            $table->integer('upah_jahit')->after('upah_potong')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahanjadis', function (Blueprint $table) {
            $table->dropColumn(['harga', 'upah_potong', 'upah_jahit']);
        });
    }
};
