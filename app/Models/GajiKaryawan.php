<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiKaryawan extends Model
{
    use HasFactory;
    protected $fillable = ['pesanan_detail_id','karyawan_id', 'peran', 'jumlah', 'upah', 'total','tanggal_dibayar'];
    public function karyawan()
    {
        return $this->belongsTo(User::class, 'karyawan_id');
    }

    public function pesananDetail()
    {
        return $this->belongsTo(PesananDetail::class, 'pesanan_detail_id');
    }

}

