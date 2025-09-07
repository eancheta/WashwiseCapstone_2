<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarWashShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'address',
        'district',
        'logo',
        'description',
        'services_offered',
        'qr_code'
    ];

    // ✅ Relationship: a shop has many feedback
    public function feedback()
    {
        return $this->hasMany(\App\Models\Feedback::class, 'shop_id');
    }
}
