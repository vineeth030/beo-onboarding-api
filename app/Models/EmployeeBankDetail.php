<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBankDetail extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeBankDetailFactory> */
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'branch_name',
        'ifsc_code',
        'proof_document_path',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
