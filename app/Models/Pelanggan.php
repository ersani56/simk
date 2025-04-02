<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;
    protected $fillable = ['kode_plg','nama_plg','alamat','telepon'];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pelanggan) {
            if (!$pelanggan->kode_plg) {
                $pelanggan->kode_plg = self::generateKodeP();
            }
        });
    }
    public static function generateKodeP()
   {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->kode_plg, 3)) + 1 : 1;
        return 'PLG' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
