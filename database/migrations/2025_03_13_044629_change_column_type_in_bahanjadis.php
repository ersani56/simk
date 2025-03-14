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
            DB::statement("ALTER TABLE bahanjadis MODIFY COLUMN satuan ENUM('Pcs', 'Stel')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bahanjadis', function (Blueprint $table) {
            //
        });
    }
};
