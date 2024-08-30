<?php

use Illuminate\Support\Facades\Route;

Route::get('', function () {
    dd(array_keys(config('timezones')));
});
