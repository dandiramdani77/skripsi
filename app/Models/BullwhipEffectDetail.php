<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullwhipEffectDetail extends Model
{
    use HasFactory;

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function bullwhip_effect()
    {
        return $this->belongsTo(BullwhipEffect::class);
    }

}
