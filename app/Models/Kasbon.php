<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jumlah',
        'tanggal_pengajuan',
        'tanggal_disetujui',
        'keterangan',
        'status',
        'gaji_id', // tambahkan ini jika belum ada
        'tanggal_lunas', // tambahkan ini jika belum ada
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_disetujui' => 'date',
        'tanggal_lunas' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gajiKaryawan()
    {
        return $this->belongsTo(GajiKaryawan::class);
    }
}
