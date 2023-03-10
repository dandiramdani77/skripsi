<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HitungRamal extends Model
{
    use HasFactory;
    protected $table = 'hitung_ramal';
    protected $fillable = ['id_kategori', 'bullwhip_effect_id'];
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function bullwhip_effect2()
    {
        return $this->belongsTo(BullwhipEffect2::class);
    }

}
