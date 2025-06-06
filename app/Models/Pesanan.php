<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = ['no_faktur', 'kode_plg', 'tanggal', 'total_tagihan', 'catatan'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            if (empty($pesanan->no_faktur)) {
                $pesanan->no_faktur = self::generateInvoiceNumber();
            }
        });
    }

    public static function generateInvoiceNumber()
    {
        $date = now()->format('dmy');
        $lastOrderToday = self::whereDate('created_at', today())->orderBy('id', 'desc')->first();

        $nextNumber = 1;
        if ($lastOrderToday && $lastOrderToday->no_faktur) {
            if (preg_match('/INV\d{6}(\d{3})$/', $lastOrderToday->no_faktur, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1;
            }
        }

        return 'INV' . $date . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class); // Foreign key: pesanan_id, Local key: id (default)
    }

    public function pelanggan()
    {
        return $this->belongsTo(\App\Models\Pelanggan::class, 'kode_plg', 'kode_plg'); // Biarkan jika ini memang benar
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id'); // 'pesanan_id' adalah FK di pembayarans
    }

    public function totalPembayaran()
    {
        return $this->pembayarans()->sum('jumlah_bayar');
    }

    public function detail(): HasMany
    {
        return $this->hasMany(PesananDetail::class); // Sama seperti pesananDetails()
    }

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
