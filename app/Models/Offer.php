<?php

namespace App\Models;

use App\Enums\OfferStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    /** @use HasFactory<\Database\Factories\OfferFactory> */
    use HasFactory;

    protected $fillable = [
        'email_attachment_content_for_client',
        'email_content_for_employee',
        'client_emails',
        'beo_emails',
        'user_id',
        'employee_id',
        'department_id',
        'name',
        'comment',
        'sign_file_path',
        'is_accepted',
        'is_declined',
        'decline_reason',
        'is_revoked',
        'revoke_reason',
        'is_family_insurance_paid_by_client',
        'status',
    ];

    protected $casts = [
        'client_emails' => 'array',
        'beo_emails' => 'array',
        'is_accepted' => 'boolean',
        'is_declined' => 'boolean',
        'is_revoked' => 'boolean',
        'is_family_insurance_paid_by_client' => 'boolean',
        'status' => OfferStatus::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }
}
