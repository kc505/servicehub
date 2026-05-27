<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'provider_profile_id',
        'service_description',
        'status',
        'scheduled_at',
        'client_note',
        'provider_note',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function providerProfile()
    {
        return $this->belongsTo(ProviderProfile::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
