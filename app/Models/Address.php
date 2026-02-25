<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'line1',
        'line2',
        'district',
        'landmark',
        'country',
        'state',
        'city',
        'pin',
        'duration_of_stay',
        'type',
        'is_present_address_same_as_current'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
