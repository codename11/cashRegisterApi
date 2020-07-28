<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id', 'total_price',
    ];

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    
}
