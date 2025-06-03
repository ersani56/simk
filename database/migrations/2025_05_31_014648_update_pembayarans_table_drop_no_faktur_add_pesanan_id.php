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
        Schema::table('pembayarans', function (Blueprint $table) {
            // 1. Tambahkan kolom pesanan_id terlebih dahulu
            //    Kita buat nullable dulu agar bisa ditambahkan ke tabel yang sudah ada datanya,
            //    dan jika Anda perlu mengisi nilainya sebelum menerapkan constraint.
            //    Jika tabel masih kosong atau Anda akan mengisi nilainya segera,
            //    Anda bisa langsung membuatnya not nullable.
            $table->unsignedBigInteger('pesanan_id')->nullable()->after('id'); // Sesuaikan 'id' dengan kolom sebelumnya jika perlu

            // !!! PERHATIAN PENTING !!!
            // Jika Anda perlu mengisi kolom 'pesanan_id' berdasarkan nilai dari 'no_faktur' lama,
            // Anda harus melakukannya DI SINI, SEBELUM menghapus 'no_faktur'.
            // Contoh (jika no_faktur di pembayarans adalah string dan merujuk ke no_faktur di pesanans):
            //
            // DB::statement('
            //     UPDATE pembayarans pb
            //     JOIN pesanans p ON pb.no_faktur = p.no_faktur
            //     SET pb.pesanan_id = p.id
            // ');
            //
            // Jika 'no_faktur' di 'pembayarans' sudah berisi 'id' dari 'pesanans',
            // maka Anda perlu langkah rename seperti diskusi kita sebelumnya, BUKAN migrasi ini.
            // Migrasi ini mengasumsikan 'pesanan_id' akan diisi dengan cara lain atau tabel baru.

            // 2. Hapus kolom no_faktur
            //    Pastikan Anda sudah mem-backup atau memigrasikan data dari kolom ini jika diperlukan.
            if (Schema::hasColumn('pembayarans', 'no_faktur')) {
                $table->dropColumn('no_faktur');
            }

            // 3. (Opsional) Jika sudah yakin semua pesanan_id terisi, buat jadi NOT NULL
            //    Jika Anda tidak mengisi data di langkah sebelumnya, Anda mungkin ingin
            //    membiarkannya nullable atau mengisi datanya terlebih dahulu.
            //    Untuk contoh ini, kita akan membuatnya NOT NULL, asumsikan data akan diisi.
            // $table->unsignedBigInteger('pesanan_id')->nullable(false)->change();
            // Jika Anda melakukan pengisian data 'pesanan_id' di atas,
            // maka setelah itu baru Anda bisa membuatnya `nullable(false)` dengan aman.
            // Untuk keamanan, jika ada data lama, biarkan nullable atau pastikan semua terisi.
            // Saya akan biarkan nullable untuk contoh ini, agar migrasi lebih aman pada tabel berisi data.
            // Anda bisa membuat migrasi terpisah untuk mengubahnya jadi NOT NULL setelah data terisi.

            // 4. Tambahkan foreign key constraint
            $table->foreign('pesanan_id')
                ->references('id')
                ->on('pesanans')
                ->onDelete('cascade'); // atau 'restrict', 'set null' sesuai kebutuhan
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // 1. Hapus foreign key constraint terlebih dahulu
            //    Laravel biasanya menamai constraint: nama_tabel_nama_kolom_foreign
            $table->dropForeign(['pesanan_id']); // Atau $table->dropForeign('pembayarans_pesanan_id_foreign');

            // 2. Hapus kolom pesanan_id
            $table->dropColumn('pesanan_id');

            // 3. Tambahkan kembali kolom no_faktur
            //    Anda perlu memutuskan tipe data dan atribut lainnya (nullable, default, dll.)
            $table->string('no_faktur')->nullable()->after('id'); // Sesuaikan tipe dan posisi
        });
    }
};
