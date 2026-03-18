<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Address;
use App\Models\Employee;
use Illuminate\Support\Facades\Gate;

class AddressController extends Controller
{
    public function index(Employee $employee)
    {
        Gate::authorize('view', $employee);

        return $employee->addresses;
    }

    public function store(StoreAddressRequest $request, Employee $employee)
    {
        Gate::authorize('update', $employee);

        $addresses = [];

        foreach ($request->validated()['addresses'] as $addressData) {
            $address = $employee->addresses()->create($addressData);
            $addresses[] = $address;
        }

        return response()->json($addresses, 201);
    }

    public function show(Employee $employee, Address $address)
    {
        Gate::authorize('view', $employee);

        return $address;
    }

    public function update(UpdateAddressRequest $request, $employee_id)
    {
        $employee = Employee::where('id', $employee_id)->first();

        Gate::authorize('update', $employee);

        $employee->addresses()->delete();

        $addresses = [];

        foreach ($request->validated()['addresses'] as $addressData) {
            $address = $employee->addresses()->create($addressData);
            $addresses[] = $address;
        }

        return response()->json($addresses);
    }

    public function destroy(Employee $employee, Address $address)
    {
        Gate::authorize('update', $employee);

        $address->delete();

        return response()->json(null, 204);
    }
}
