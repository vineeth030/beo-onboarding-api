<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index() {

        return response()->json(auth()->user()->unreadNotifications);
    }

    public function readAll() {
        
        auth()->user()->unreadNotifications->markAsRead();
        return response()->json(['success' => true]);
    }
}
