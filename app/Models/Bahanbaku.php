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
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bahanbaku) {
            if (!$bahanbaku->kode_bbaku) {
                $bahanbaku->kode_bbaku = self::generateKodeBB();
            }
        });
    }

    public function stok(): HasMany
    {
        return $this->hasMany(Stok::class, 'kode_bbaku', 'kode_bbaku');
    }

    public static function generateKodeBB()
    {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->kode_bbaku, 2)) + 1 : 1;
        return 'BB' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
