<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BeoEmployee extends Model
{
    protected $fillable = [
        'name',
        'employee_id',
        'photo_path',
        'designation',
        'phone',
        'email'
    ];
}
