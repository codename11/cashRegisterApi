<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'name', 'barcode', 'price',
    ];

    public function order_details()
    {
        return $this->hasMany('App\Order_Details', "order_details_id", "id");
    }

}
