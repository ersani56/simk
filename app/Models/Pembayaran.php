<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;
    protected $fillable = [
        'no_faktur',
        'tanggal_bayar',
        'jumlah_bayar',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'no_faktur', 'no_faktur');
    }
}
