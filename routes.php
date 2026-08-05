<?php

use Illuminate\Http\Request;
use Xitara\ERecht24\Classes\PushService;

Route::post('/api/erecht24/push', function (Request $request) {
    return (new PushService())->handle($request);
});
