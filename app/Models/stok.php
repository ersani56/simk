<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class stok extends Model
{
    use HasFactory;
    protected $fillable = ['kode_stok','nama_bbaku','kode_bbaku','jml_stok','lokasi'];
}
