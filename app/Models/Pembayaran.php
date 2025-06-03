<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'pesanan_id',
        'tanggal_bayar',
        'jumlah_bayar',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
        //return $this->belongsTo(Pesanan::class, 'pesanan_id', 'id');
    }
}
