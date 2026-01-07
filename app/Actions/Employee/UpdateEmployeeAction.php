<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;

class UpdateEmployeeAction
{
    public function execute(Employee $employee, array $data, ?UploadedFile $file = null): Employee
    {
        // Handle file upload
        if ($file) {
            $path = $file->store("documents/{$employee->id}", 'public');
            $data['photo_path'] = '/storage/' . $path;
        }

        // Update employee
        $employee->update($data);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'update.details.candidate',
            'title' => (($data['status'] ?? null) != 4) ? 
                            'Details of candidate ' . $employee->name . ' updated by ' . auth()->user()->name : 
                            'Details of candidate ' . $employee->name . ' verified by ' . auth()->user()->name,
        ]);

        return $employee;
    }
}
