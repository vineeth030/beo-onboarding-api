<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    use HasFactory;

    const STATUS_YET_TO_START = 0;

    const STATUS_STARTED = 1;

    const STATUS_IN_PROGRESS = 2;

    const STATUS_SUCCESS = 3;

    const STATUS_REJECTED = 4;

    const OFFER_STATUS_NOT_STARTED = 0;

    const OFFER_STATUS_PENDING = 1;

    const OFFER_STATUS_ACCEPTED = 2;

    const OFFER_STATUS_REJECTED = 3;

    const DIVISION_BEO_INDIA = 0;

    const DIVISION_INDIA_4IT = 1;

    const CATEGORY_EXPERIENCED = 0;

    const CATEGORY_FRESHER = 1;

    const CATEGORY_INTERN = 2;

    protected $fillable = [
        'user_id',
        'department_id',
        'office_id',
        'designation_id',
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
        'password',
        'mobile',
        'photo_path',
        'blood_group',
        'joining_date',
        'status',
        'division',
        'category',
        'is_verified',
        'is_pre_joining_form_downloaded',
        'is_joining_date_update_approved',
        'is_onboarded',
        'updated_joining_date',
        'requested_joining_date',
        'is_open',
        'buddy_id',
        'poc_1_id',
        'poc_2_id',
    ];

    protected $casts = [
        'status' => 'integer',
        'division' => 'integer',
        'category' => 'integer',
        'background_verification_started_at' => 'datetime',
        'last_bv_reminder_sent_at' => 'datetime',
    ];

    protected $appends = ['full_name'];

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

    public function getDivisionLabelAttribute()
    {
        return match ($this->division) {
            self::DIVISION_BEO_INDIA => 'BEO-India',
            self::DIVISION_INDIA_4IT => 'India-4IT',
            default => 'unknown',
        };
    }

    public function getCategoryLabelAttribute()
    {
        return match ($this->division) {
            self::CATEGORY_EXPERIENCED => 'EXPERIENCED',
            self::CATEGORY_FRESHER => 'FRESHER',
            self::CATEGORY_INTERN => 'INTERN',
            default => 'unknown',
        };
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function client(): HasOne
    {
        return $this->hasOne(Client::class);
    }

    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
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

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function activeOffer(): HasOne
    {
        return $this->hasOne(Offer::class)->where('is_revoked', false);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }
}
