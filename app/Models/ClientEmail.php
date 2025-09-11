<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientEmail extends Model
{
    /** @use HasFactory<\Database\Factories\ClientEmailFactory> */
    use HasFactory;

    protected $fillable = ['client_id', 'email'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
