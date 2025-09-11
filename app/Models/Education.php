<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    use HasFactory;

    protected $table = 'educations'; // Since educations is against the english grammer rules.

    protected $fillable = [
        'employee_id',
        'board',
        'school',
        'specialization',
        'percentage',
        'from_date',
        'to_date',
        'mode_of_education',
        'certificate_path',
        'certificate_preview_url',
        'is_highest',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
