<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BullwhipEffect extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

    public function bullwhip_effect_detail()
    {
        return $this->hasMany(BullwhipEffectDetail::class);
    }
}
