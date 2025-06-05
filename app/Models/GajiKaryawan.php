<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
{
    use HasFactory;
    protected $fillable = ['pesanan_detail_id','karyawan_id', 'peran', 'jumlah', 'upah', 'total','tanggal_dibayar'];
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function pesananDetail()
    {
        return $this->belongsTo(PesananDetail::class, 'pesanan_detail_id');
    }

    protected static function booted()
    {
        static::saved(function ($model) {
            $model->pesananDetail->update();
        });

        static::deleted(function ($model) {
            $model->pesananDetail->updateStatus();
        });
    }


    protected $casts = [
        'gaji_pokok' => 'decimal:2',
        'tunjangan_lain' => 'decimal:2',
        'potongan_kasbon' => 'decimal:2',
        'potongan_lain' => 'decimal:2',
        'tanggal_pembayaran' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Kasbon yang dipotong pada gaji ini
    public function kasbonsDipotong()
    {
        return $this->hasMany(Kasbon::class, 'gaji_id');
    }

    // Accessor untuk Gaji Bersih jika tidak menggunakan storedAs
    public function getGajiBersihAttribute()
    {
        return ($this->gaji_pokok + $this->tunjangan_lain) - ($this->potongan_kasbon + $this->potongan_lain);
    }


}

