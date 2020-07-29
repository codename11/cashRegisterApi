<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'total_price',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', "user_id", "id");
    }
    
    public function order_details()
    {
        return $this->hasMany('App\Order_Details', "order_id", "id");
    }

}
