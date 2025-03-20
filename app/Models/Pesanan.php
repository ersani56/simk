<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    use HasFactory;
    protected $fillable = ['no_faktur','kode_plg','kode_bjadi','jumlah','ukuran','harga','catatan','status'];
}
