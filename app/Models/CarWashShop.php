<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;

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
    'status',
    'qr_code',
    'qr_code2',
    'qr_code3',
    'qr_code4',
    'qr_code5',
    'latitude',
    'longitude'
];

    /**
     * Get the owner that owns the CarWashShop.
     */
    public function owner()
    {
        return $this->belongsTo(CarWashOwner::class);
    }

    /**
     * Get the full URL for the car wash logo.
     * This accessor fixes the mixed content error by checking for absolute URLs.
     */
    public function getLogoAttribute($value)
    {
        // ✅ If the value starts with 'http', it's already a full URL (from Cloudinary)
        if ($value && str_starts_with($value, 'http')) {
            // Return the value as is.
            return $value;
        }

        // ❌ Otherwise, assume it's a local file path and prepend the storage URL.
        return $value ? URL::to('/storage/' . $value) : null;
    }

    /**
     * Get the full URL for the QR code.
     * This accessor fixes the mixed content error by checking for absolute URLs.
     */
    public function getQrCodeAttribute($value)
    {
        // ✅ If the value starts with 'http', it's already a full URL (from Cloudinary)
        if ($value && str_starts_with($value, 'http')) {
            // Return the value as is.
            return $value;
        }

        // ❌ Otherwise, assume it's a local file path and prepend the storage URL.
        return $value ? URL::to('/storage/' . $value) : null;
    }

    // A shop has many feedback entries
    public function feedback()
    {
        return $this->hasMany(\App\Models\Feedback::class, 'shop_id');
    }
}
