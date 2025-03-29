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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pesanan) {
            $pesanan->no_faktur = self::generateInvoiceNumber();
        });
    }

    public static function generateInvoiceNumber()
    {
        $date = now()->format('dmy'); // Format: 250324 (25 Maret 2024)
        $lastOrder = self::whereDate('tanggal', now())->latest('id')->first();
        $nextNumber = $lastOrder ? ((int) substr($lastOrder->no_faktur, -3)) + 1 : 1;

        return 'INV' . $date . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }
    public function getNoFakturAttribute($value)
    {
        return $value ?? self::generateInvoiceNumber();
    }

    public function pesananDetails(): HasMany
    {
        return $this->hasMany(PesananDetail::class, 'no_faktur', 'no_faktur');
    }
}
