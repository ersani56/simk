<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bahanbaku extends Model
{
    //use HasFactory;
    protected $table = 'bahanbakus';
    protected $fillable = ['kode_bbaku','nama_bbaku','satuan','harga'];

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class, 'kode_bbaku', 'kode_bbaku');
    }
}
