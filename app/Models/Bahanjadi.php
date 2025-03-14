<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahanjadi extends Model
{
    use HasFactory;
    protected $fillable = ['kode_bjadi','nama_bjadi','kategori','satuan','upah','gambar1','gambar2'];
}
