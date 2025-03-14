<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class stok extends Model
{
    use HasFactory;
    protected $fillable = ['kode_stok','kode_bbaku','nama_bbaku','jml_stok','lokasi'];

    public function bahanBaku(): BelongsTo
    {
        return $this->belongsTo(Bahanbaku::class, 'kode_bbaku', 'kode_bbaku');
    }
}
