<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function PHPSTORM_META\map;

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
        'decline_reason'
    ];

    protected $casts = [
        'client_emails' => 'array',
        'beo_emails' => 'array',
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