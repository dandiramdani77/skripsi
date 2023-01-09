<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullwhipEffectDetail extends Model
{
    use HasFactory;

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk', 'id_produk');
    }

    public function bullwhip_effect()
    {
        return $this->belongsTo(BullwhipEffect::class);
    }
}
