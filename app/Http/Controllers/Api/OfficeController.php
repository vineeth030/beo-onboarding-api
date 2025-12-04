<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Office;
use Illuminate\Http\Request;

class OfficeController extends Controller
{
    /**
     * Display a listing of the office.
     */
    public function index()
    {
        return Office::all();
    }

    /**
     * Display the specified office.
     */
    public function show(Office $office)
    {
        return $office;
    }
}
