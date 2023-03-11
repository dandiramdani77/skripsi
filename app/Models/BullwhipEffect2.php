<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BullwhipEffect extends Model
{
    use HasFactory;

   

    public function bullwhip_effect_detail2()
    {
        return $this->hasMany(BullwhipEffectDetail2::class);
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
        return $query->join('bullwhip_effect_details2', 'bullwhip_effects2.id', '=', 'bullwhip_effect_details2.bullwhip_effect_id')
                     ->join('kategori', 'bullwhip_effect_details2.id_kategori', '=', 'kategori.id_kategori')
                     ->select('bullwhip_effects2.*', 'kategori.nama_kategori as nama_kategori')
                     ->distinct('bullwhip_effects2.id');
    }
}
