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
            $table->integer('upah_potong')->after('harga')->nullable();
            $table->integer('upah_jahit')->after('upah_potong')->nullable();
            $table->integer('upah_sablon')->after('upah_jahit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
            $table->dropColumn(['upah_potong', 'upah_jahit','upah_sablon']);
        });
    }
};
