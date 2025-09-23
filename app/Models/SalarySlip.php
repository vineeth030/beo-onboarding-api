<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySlip extends Model
{
    use HasFactory;

    protected $fillable = [
        'employment_id',
        'file_path',
        'preview_url',
    ];

    public function employment(): BelongsTo
    {
        return $this->belongsTo(Employment::class);
    }
}
