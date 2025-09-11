<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'salary_file',
        'salary_preview_url',
        'relieving_file',
        'relieving_preview_url',
        'is_current_org',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
