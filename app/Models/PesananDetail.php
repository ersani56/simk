<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    // use HasFactory;
    protected $table ='pesanan_details';

    protected $fillable = [
        'no_faktur',
        'kode_bjadi',
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
        'keterangan',
        'setelan',
    ];

    public function updateIfNull(string $column, $value)
    {
        if (is_null($this->$column)) {
            $this->update([$column => $value]);
        }
    }
    public function bahanjadi()
    {
        return $this->belongsTo(Bahanjadi::class, 'kode_bjadi', 'kode_bjadi');
    }

    public function pemotongUser()
    {
        return $this->belongsTo(User::class, 'pemotong');
    }

    public function penjahitUser()
    {
        return $this->belongsTo(User::class, 'penjahit');
    }

    public function penyablonUser()
    {
        return $this->belongsTo(User::class, 'penyablon');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'no_faktur', 'no_faktur');
    }



}
