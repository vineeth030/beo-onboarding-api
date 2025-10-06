<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'company_name',
        'employee_id_at_company',
        'designation',
        'location',
        'mode_of_employment',
        'start_date',
        'last_working_date',
        'resignation_acceptance_letter_file',
        'resignation_acceptance_letter_preview_url',
        'experience_letter_file',
        'experience_letter_preview_url',
        'is_current_org',
        'is_serving_notice_period',
        'is_verified',
        'is_open'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function salarySlips(): HasMany
    {
        return $this->hasMany(SalarySlip::class);
    }
}
