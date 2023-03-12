<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BullwhipEffect2 extends Model
{
    use HasFactory;

    protected $table = 'bullwhip_effects2';

    public function hitungramal()
    {
        return $this->hasMany(HitungRamal::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function scopeGetEffectsWithCategories($query)
    {
        return $query->with('hitung_ramal.kategori');
    }

    public function scopeGetEffectsWithCategoryName($query)
    {
        return $query->join('hitung_ramal', 'bullwhip_effects2.id', '=', 'hitung_ramal.bullwhip_effect_id')
                     ->join('kategori', 'hitung_ramal.id_kategori', '=', 'kategori.id_kategori')
                     ->select('bullwhip_effects2.*', 'kategori.nama_kategori as nama_kategori')
                     ->distinct('bullwhip_effects2.id');
    }
}
