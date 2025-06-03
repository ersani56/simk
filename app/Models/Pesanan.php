<?php

namespace App\Models;

// use App\Models\PesananDetail; // Tidak perlu di-import jika hanya dipakai di return type hint
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    // Nama tabel 'pesanans' dan primary key 'id' sudah sesuai konvensi,
    // jadi tidak perlu mendefinisikan $table atau $primaryKey secara eksplisit.

    protected $fillable = ['no_faktur', 'kode_plg', 'tanggal', 'total_tagihan', 'catatan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            // Jika no_faktur tidak di-set manual saat create, generate otomatis
            if (empty($pesanan->no_faktur)) {
                $pesanan->no_faktur = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber()
    {
        // Menggunakan tanggal dari pesanan jika ada, jika tidak, tanggal sekarang
        // Ini lebih relevan jika generateInvoiceNumber dipanggil dari luar event 'creating'
        // Namun dalam konteks event 'creating', 'now()' biasanya cukup.
        $date = now()->format('dmy');
        // Ambil pesanan terakhir berdasarkan ID untuk mendapatkan urutan berikutnya,
        // tidak lagi berdasarkan no_faktur yang bisa jadi tidak berurutan jika diinput manual.
        // Filter berdasarkan tanggal juga penting untuk reset nomor harian.
        $lastOrderToday = self::whereDate('created_at', today())->orderBy('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastOrderToday && $lastOrderToday->no_faktur) {
            // Ekstrak nomor urut dari no_faktur terakhir HARI INI
            // Asumsi format INVddmmyyXXX
            if (preg_match('/INV\d{6}(\d{3})$/', $lastOrderToday->no_faktur, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }
        }

        return 'INV' . $date . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Accessor getNoFakturAttribute bisa dipertimbangkan untuk dihapus jika
    // no_faktur selalu di-generate saat 'creating' dan wajib ada.
    // Jika no_faktur bisa null dan Anda ingin fallback, maka biarkan.
    // public function getNoFakturAttribute($value)
    // {
    //     return $value ?? self::generateInvoiceNumber();
    // }

    /**
     * Mendapatkan semua detail untuk pesanan ini.
     * Laravel akan otomatis mencari foreign key 'pesanan_id' di tabel pesanan_details.
     */
    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class); // Foreign key: pesanan_id, Local key: id (default)
    }

    public function pelanggan()
    {
        // Asumsi tabel pelanggans memiliki PK 'kode_plg'
        // Jika PK tabel pelanggans adalah 'id', maka seharusnya:
        // return $this->belongsTo(\App\Models\Pelanggan::class, 'kode_plg', 'id');
        // atau jika foreign key di pesanans adalah 'pelanggan_id' dan PK pelanggan adalah 'id':
        // return $this->belongsTo(\App\Models\Pelanggan::class);
        return $this->belongsTo(\App\Models\Pelanggan::class, 'kode_plg', 'kode_plg'); // Biarkan jika ini memang benar
    }

    /**
     * Mendapatkan semua pembayaran untuk pesanan ini.
     * DIASUMSIKAN tabel pembayarans MASIH menggunakan 'no_faktur' sebagai foreign key.
     * Jika tabel pembayarans juga sudah diubah menggunakan 'pesanan_id', maka ini harus menjadi:
     * return $this->hasMany(Pembayaran::class);
     */
    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id'); // 'pesanan_id' adalah FK di pembayarans
    }

    public function totalPembayaran()
    {
        return $this->pembayarans()->sum('jumlah_bayar');
    }

    /**
     * Alias untuk pesananDetails(), bisa dihapus jika tidak digunakan
     * atau jika ingin konsisten dengan nama 'pesananDetails'.
     */
    public function detail(): HasMany
    {
        return $this->hasMany(PesananDetail::class); // Sama seperti pesananDetails()
    }

    // Accessor ini akan menghitung total tagihan berdasarkan detail pesanan.
    // Jika Anda sudah menyimpan 'total_tagihan' di tabel pesanans dan itu yang jadi acuan,
    // accessor ini mungkin tidak diperlukan atau perlu logika berbeda.
    // public function getTotalTagihanAttribute()
    // {
    //     // Pastikan relasi pesananDetails sudah benar
    //     return $this->pesananDetails->sum(function ($item) {
    //         return $item->harga * $item->jumlah;
    //     });
    // }

    public function getTotalBayarAttribute()
    {
        return $this->pembayarans->sum('jumlah_bayar'); // Menggunakan relasi pembayaran()
    }

    public function getSisaTagihanAttribute()
    {
        // Pastikan $this->total_tagihan adalah nilai yang benar (dari database atau accessor lain)
        return $this->total_tagihan - $this->total_bayar;
    }
}
