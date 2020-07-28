<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $fillable = [
        'name', 'barcode', 'price',
    ];

    public function Order_Details()
    {
        return $this->belongsToMany('App\Order_Details');
    }

}
