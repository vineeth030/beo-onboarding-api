<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

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
    ];

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
