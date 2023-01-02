<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $table = 'order';
    protected $primaryKey = 'id_order';
    protected $guarded = [];

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'id_distributor', 'id_distributor');
    }
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id_user');
    }
    public function orderdetail()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
