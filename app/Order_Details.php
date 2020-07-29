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
        return $this->belongsTo('App\Orders', "order_id", "id");
    }

    public function articles()
    {
        return $this->hasMany('App\Article', "article_id", "id");
    }

}
