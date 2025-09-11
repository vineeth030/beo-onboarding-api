<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    const STATUS_YET_TO_START = 0;
    const STATUS_STARTED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_SUCCESS = 3;
    const STATUS_REJECTED = 4;

    const OFFER_STATUS_PENDING = 0;
    const OFFER_STATUS_ACCEPTED = 1;
    const OFFER_STATUS_REJECTED = 2;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'fathers_name',
        'dob',
        'gender',
        'marital_status',
        'nationality',
        'place_of_birth',
        'email',
        'mobile',
        'photo_path',
        'blood_group',
        'status',
        'offer_letter_status',
    ];

    protected $casts = [
        'status' => 'integer',
        'offer_letter_status' => 'integer',
    ];

    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            self::STATUS_YET_TO_START => 'yet_to_start',
            self::STATUS_STARTED => 'started',
            self::STATUS_IN_PROGRESS => 'in_progress',
            self::STATUS_SUCCESS => 'success',
            self::STATUS_REJECTED => 'rejected',
            default => 'unknown',
        };
    }

    public function getOfferLetterStatusLabelAttribute()
    {
        return match ($this->offer_letter_status) {
            self::OFFER_STATUS_PENDING => 'pending',
            self::OFFER_STATUS_ACCEPTED => 'accepted',
            self::OFFER_STATUS_REJECTED => 'rejected',
            default => 'unknown',
        };
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class);
    }

    public function employments(): HasMany
    {
        return $this->hasMany(Employment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
