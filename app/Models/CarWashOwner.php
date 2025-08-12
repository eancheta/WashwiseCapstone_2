<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CarWashOwner extends Authenticatable
{
    use HasFactory;

    protected $table = 'car_wash_owners';

    protected $fillable = [
        'name',
        'email',
        'password',
        'district',
        'address',
        'photo1',
        'photo2',
        'photo3',
        'verification_code',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
