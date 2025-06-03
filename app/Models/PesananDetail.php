<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PesananDetail extends Model
{
    use HasFactory; // Tambahkan jika Anda menggunakan factory

    // Nama tabel 'pesanan_details' sudah sesuai konvensi,
    // jadi tidak perlu mendefinisikan $table secara eksplisit.
    // protected $table = 'pesanan_details';

    // Primary key sekarang adalah 'id' (default Laravel), jadi baris ini bisa dihapus.
    // Jika Anda tetap ingin eksplisit: protected $primaryKey = 'id';
    // HAPUS: protected $primaryKey = 'no_faktur';

    protected $fillable = [
        'pesanan_id',    // TAMBAHKAN INI! Foreign key ke tabel pesanans
        'kode_bjadi',
        'setelan',
        'satuan',
        'ukuran',
        'harga',
        'upah_potong',
        'upah_jahit',
        'upah_sablon',
        'jumlah',
        'status',
        'pemotong',
        'penjahit',
        'penyablon',
        'ket',
        'is_pasangan',
    ];

    protected $casts = [
        'is_pasangan' => 'boolean',
        'harga' => 'decimal:2', // Contoh jika harga adalah desimal
        'jumlah' => 'integer',  // Contoh
    ];

    // Kolom no_faktur tidak lagi primary key dan tidak auto-incrementing
    // public $incrementing = false; // HAPUS JIKA SEBELUMNYA ADA KARENA NO_FAKTUR PK
    // protected $keyType = 'string'; // HAPUS JIKA SEBELUMNYA ADA KARENA NO_FAKTUR PK

    protected static function booted()
    {
        static::saved(function ($model) {
            // $model->updateStatus(); // Logika ini sudah dipindahkan ke accessor getStatusAttribute
                                    // Jika Anda ingin status disimpan di DB, maka biarkan
                                    // Tapi pastikan tidak ada infinite loop dengan saveQuietly()
                                    // Pertimbangkan menggunakan observer terpisah untuk update status di DB
        });

        static::deleted(function ($model) {
            // $model->updateStatusAfterDelete(); // Sama seperti di atas
        });
    }

    // Jika status ingin dihitung secara dinamis dan tidak disimpan di DB,
    // maka method updateStatus dan updateStatusAfterDelete serta event di booted() bisa dipertimbangkan untuk dihapus
    // dan hanya mengandalkan getStatusAttribute().
    // Jika status TETAP disimpan di DB, pastikan logika ini tidak menyebabkan infinite loop save.
    // Pertimbangkan:
    // 1. Menggunakan `saveQuietly()` jika perubahan status tidak boleh memicu event lain.
    // 2. Memindahkan logika update status ke observer.

    /*
    // CONTOH jika status mau diupdate di DB via event (hati-hati infinite loop)
    public function calculateAndSetStatus()
    {
        $currentStatus = $this->attributes['status'] ?? null; // Ambil status mentah
        $newStatus = $this->determineStatus(); // Panggil method yang menghitung status

        if ($newStatus !== $currentStatus) {
            $this->status = $newStatus;
            $this->saveQuietly(); // Simpan tanpa memicu event lagi
        }
    }

    protected function determineStatus() {
        // Logika dari getStatusAttribute Anda pindah ke sini
        $id = $this->id; // $this->id sekarang adalah PK yang benar
        $jumlah = $this->jumlah;

        $dipotong = GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'pemotong')->sum('jumlah');
        $dijahit = GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'penjahit')->sum('jumlah');
        $disablon = GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'penyablon')->sum('jumlah');

        if ($dipotong >= $jumlah && $dijahit >= $jumlah && $disablon >= $jumlah) {
            return 'selesai';
        }
        if ($dipotong == 0 && $dijahit == 0 && $disablon == 0) {
            return 'antrian';
        }
        return 'proses';
    }

    // Panggil ini dari event 'saved'
    // static::saved(function ($model) {
    //     $model->calculateAndSetStatus();
    // });
    */


    public function updateIfNull(string $column, $value)
    {
        if (is_null($this->$column)) {
            $this->update([$column => $value]);
        }
    }

    public function produk(): BelongsTo
    {
        return $this->belongsTo(Produk::class, 'kode_bjadi', 'kode_bjadi');
    }

    public function pemotongUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemotong'); // Asumsi 'pemotong' adalah FK ke 'users.id'
    }

    public function penjahitUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penjahit'); // Asumsi 'penjahit' adalah FK ke 'users.id'
    }

    public function penyablonUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyablon'); // Asumsi 'penyablon' adalah FK ke 'users.id'
    }

    /**
     * Mendapatkan pesanan yang memiliki detail ini.
     * Laravel akan otomatis mencari foreign key 'pesanan_id' di tabel ini
     * dan mencocokkannya dengan 'id' di tabel pesanans.
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class); // Foreign key: pesanan_id, Owner key: id (default)
    }

    // $touches akan mengupdate timestamp 'updated_at' pada model Pesanan
    // setiap kali PesananDetail disimpan atau diupdate. Ini sudah benar.
    protected $touches = ['pesanan'];

    public function scopeMainItems($query)
    {
        return $query->where('is_pasangan', false);
    }

    public function scopePairItems($query)
    {
        return $query->where('is_pasangan', true);
    }

    /**
     * Relasi ke GajiKaryawan.
     * Asumsi tabel gaji_karyawans memiliki foreign key 'pesanan_detail_id'
     * yang merujuk ke 'id' di tabel pesanan_details.
     */
    public function gajiKaryawans(): HasMany // Tambahkan return type hint
    {
        return $this->hasMany(GajiKaryawan::class, 'pesanan_detail_id'); // FK: pesanan_detail_id, Local Key: id (default)
    }

    /**
     * Accessor untuk mendapatkan status secara dinamis.
     * Ini akan menghitung status setiap kali Anda mengakses $pesananDetail->status.
     * Jika Anda ingin menyimpan status di database, Anda perlu mekanisme lain (event/observer).
     */
    public function getStatusAttribute($value) // Terima $value (status dari DB) jika ada
    {
        // Jika Anda memutuskan untuk menyimpan status di DB dan HANYA mengandalkan nilai DB,
        // Anda bisa return $value; di sini dan pastikan logika update di event sudah benar.
        // Jika Anda ingin selalu menghitungnya secara dinamis:

        $id = $this->attributes['id']; // Gunakan $this->attributes['id'] untuk menghindari rekursi jika ada accessor 'id'
        $jumlah = $this->attributes['jumlah'];

        // Pastikan GajiKaryawan juga menggunakan namespace yang benar
        $dipotong = \App\Models\GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'pemotong')->sum('jumlah');
        $dijahit = \App\Models\GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'penjahit')->sum('jumlah');
        $disablon = \App\Models\GajiKaryawan::where('pesanan_detail_id', $id)->where('peran', 'penyablon')->sum('jumlah');

        if ($jumlah > 0) { // Hindari division by zero atau logika aneh jika jumlah 0
            if ($dipotong >= $jumlah && $dijahit >= $jumlah && $disablon >= $jumlah) {
                return 'selesai';
            }
        }

        if ($dipotong == 0 && $dijahit == 0 && $disablon == 0) {
            return 'antrian';
        }

        return 'proses';
    }

    public function updateStatus()
    {
        $totalPekerjaan = $this->jumlah;

        // Cek jika semua peran terpenuhi
        $peran = ['pemotong', 'penjahit', 'penyablon'];
        $peranTerpenuhi = true;
        foreach ($peran as $p) {
            $jumlahPeran = GajiKaryawan::where('pesanan_detail_id', $this->id)
                ->where('peran', $p)
                ->sum('jumlah');
            if ($jumlahPeran < $totalPekerjaan) {
                $peranTerpenuhi = false;
                break;
            }
        }

        if ($peranTerpenuhi) {
            $this->status = 'selesai';
        } elseif (GajiKaryawan::where('pesanan_detail_id', $this->id)->exists()) {
            $this->status = 'proses';
        } else {
            $this->status = 'antrian';
        }

        $this->saveQuietly();
    }

}
