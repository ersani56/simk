<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bahanbaku extends Model
{
    use HasFactory;
    protected $fillable = ['kode_bbaku','nama_bbaku','satuan','harga'];
}
