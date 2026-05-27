<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'trade_category',
        'bio',
        'location',
        'phone',
        'hourly_rate',
        'is_available',
        'average_rating',
        'total_reviews',
    ];

    protected $casts = [
        'is_available'   => 'boolean',
        'average_rating' => 'float',
        'hourly_rate'    => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolioImages()
    {
        return $this->hasMany(PortfolioImage::class);
    }
    public function bookings()
{
    return $this->hasMany(Booking::class);
}

public function reviews()
{
    return $this->hasMany(Review::class);
}
}
