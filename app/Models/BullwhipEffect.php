<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BullwhipEffect extends Model
{
    use HasFactory;

   

    public function bullwhip_effect_detail()
    {
        return $this->hasMany(BullwhipEffectDetail::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function scopeGetEffectsWithCategories($query)
    {
        return $query->with('bullwhip_effect_detail.kategori');
    }

    public function scopeGetEffectsWithCategoryName($query)
    {
        return $query->join('bullwhip_effect_details', 'bullwhip_effects.id', '=', 'bullwhip_effect_details.bullwhip_effect_id')
                     ->join('kategori', 'bullwhip_effect_details.id_kategori', '=', 'kategori.id_kategori')
                     ->select('bullwhip_effects.*', 'kategori.nama_kategori as nama_kategori')
                     ->distinct('bullwhip_effects.id');
    }
}
