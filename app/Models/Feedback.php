<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $fillable = ['shop_id', 'user_id', 'comment', 'rating'];

    public function shop()
    {
        return $this->belongsTo(CarWashShop::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
