<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PesananDetail extends Model
{
    protected $table = 'pesanan_details';

    protected $fillable = [
        'no_faktur',
        'kode_bjadi',
        'setelan',       // Untuk grouping produk utama-pasangan
        'satuan',        // 'pcs', 'stel', 'paket'
        'ukuran',
        'harga',
        'upah_potong',
        'upah_jahit',
        'upah_sablon',
        'jumlah',
        'status',
        'pemotong',      // ID user pemotong
        'penjahit',      // ID user penjahit
        'penyablon',     // ID user penyablon
        'ket',
        'is_pasangan',   // Tambahkan ini untuk flag produk pasangan
    ];

    // Tambahkan casting untuk boolean
    protected $casts = [
        'is_pasangan' => 'boolean',
    ];

    public function updateIfNull(string $column, $value)
    {
        if (is_null($this->$column)) {
            $this->update([$column => $value]);
        }
    }

    // Relasi dengan nama lebih konsisten (camelCase)
    public function bahanJadi(): BelongsTo
    {
        return $this->belongsTo(Bahanjadi::class, 'kode_bjadi', 'kode_bjadi');
    }

    public function pemotongUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pemotong');
    }

    public function penjahitUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penjahit');
    }

    public function penyablonUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penyablon');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'no_faktur', 'no_faktur');
    }

    protected $touches = ['pesanan'];

    // Scope untuk produk utama
    public function scopeMainItems($query)
    {
        return $query->where('is_pasangan', false);
    }

    // Scope untuk produk pasangan
    public function scopePairItems($query)
    {
        return $query->where('is_pasangan', true);
    }
}
