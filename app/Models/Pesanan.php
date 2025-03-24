<?php

namespace App\Models;

use App\Models\PesananDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pesanan extends Model
{
    use HasFactory;

    protected $fillable = ['no_faktur', 'kode_plg', 'tanggal'];

    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'no_faktur', 'no_faktur');
    }
}
