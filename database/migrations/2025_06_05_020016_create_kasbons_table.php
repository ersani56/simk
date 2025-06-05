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
        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Relasi ke user (karyawan)
            $table->decimal('jumlah', 15, 2); // Jumlah kasbon
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak', 'lunas'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->foreignId('approved_by_id')->nullable()->constrained('users')->nullOnDelete(); // Siapa yang menyetujui
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kasbons');
    }
};
