<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAddressRequest;
use App\Http\Requests\UpdateAddressRequest;
use App\Models\Employee;
use App\Models\Address;

class AddressController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->addresses;
    }

    public function store(StoreAddressRequest $request, Employee $employee)
    {
        $addresses = [];

        foreach ($request->validated()['addresses'] as $addressData) {
            $address = $employee->addresses()->create($addressData);
            $addresses[] = $address;
        }

        return response()->json($addresses, 201);
    }

    public function show(Employee $employee, Address $address)
    {
        return $address;
    }

    public function update(UpdateAddressRequest $request, Employee $employee, Address $address)
    {
        $address->update($request->validated());
        return response()->json($address);
    }

    public function destroy(Employee $employee, Address $address)
    {
        $address->delete();
        return response()->json(null, 204);
    }
}
