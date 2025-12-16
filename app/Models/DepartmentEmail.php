<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentEmail extends Model
{
    protected $fillable = ['department_id', 'email'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
