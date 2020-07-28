<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order_Details extends Model
{
    protected $fillable = [
        'order_id', 'article_id', 'quantity',
    ];

    public function orders()
    {
        return $this->belongsToMany('App\Orders');
    }

    public function articles()
    {
        return $this->belongsToMany('App\Article');
    }

}
