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
            $table->dropForeign('pesanan_details_no_faktur_foreign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_details', function (Blueprint $table) {
                    $table->foreign('no_faktur')
              ->references('no_faktur')
              ->on('pesanans')
              ->onDelete('cascade'); // Sesuaikan dengan aksi sebelumnya
        });
    }
};
