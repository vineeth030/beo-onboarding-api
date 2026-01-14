<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-mail', function () {
    Mail::raw('Local SMTP test successful!', function ($message) {
        $message->to('vineeth030@gmail.com')
                ->subject('Laravel Local SMTP Test');
    });

    return 'Mail sent!';
});
