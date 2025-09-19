<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int|null $district
 * @property string|null $address
 * @property string|null $photo1
 * @property string|null $photo2
 * @property string|null $photo3
 * @property string|null $verification_code
 * @property int $is_verified
 * @property string $status
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CarWashShop|null $shop
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereIsVerified($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner wherePhoto1($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner wherePhoto2($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner wherePhoto3($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashOwner whereVerificationCode($value)
 */
	class CarWashOwner extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $owner_id
 * @property string $name
 * @property string $address
 * @property int $district
 * @property string|null $logo
 * @property string|null $description
 * @property string|null $services_offered
 * @property string|null $qr_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Feedback> $feedback
 * @property-read int|null $feedback_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereOwnerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereQrCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereServicesOffered($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CarWashShop whereUpdatedAt($value)
 */
	class CarWashShop extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $shop_id
 * @property int $user_id
 * @property string $comment
 * @property int $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CarWashShop $shop
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereShopId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Feedback whereUserId($value)
 */
	class Feedback extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $verification_code
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CarWashShop> $shops
 * @property-read int|null $shops_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereVerificationCode($value)
 */
	class User extends \Eloquent {}
}

