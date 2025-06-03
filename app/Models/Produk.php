<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;
    protected $fillable = ['kode_bjadi','nama_bjadi','kategori','harga','upah_potong','upah_jahit','upah','gambar1','gambar2'];
    public static function generateKodeP($kategori)
    {
        if (!$kategori) {
            return null; // Jika kategori belum dipilih
        }

        // Ambil huruf pertama dari kategori
        $prefix = strtoupper(substr($kategori, 0, 1));

        // Cari produk terakhir dengan prefix yang sama
        $last = self::where('kode_bjadi', 'LIKE', "$prefix%")->latest('id')->first();

        // Ambil nomor urut terakhir, jika tidak ada mulai dari 1
        $number = $last ? ((int) substr($last->kode_bjadi, 1)) + 1 : 1;

        // Format kode produk (misal: E000001, F000002, dll)
        return $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($produk) {
            if (!$produk->kode_bajdi) {
                $produk->kode_bjadi = self::generateKodeP($produk->kategori);
            }
        });
    }

}
