<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    // use HasFactory;
    protected $fillable = ['no_faktur', 'kode_bjadi', 'ukuran', 'harga', 'jumlah', 'status'];

    public function pesananDetails():BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'no_faktur', 'no_faktur');
    }
}
