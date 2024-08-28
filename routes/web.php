<?php

use Illuminate\Support\Facades\Route;

Route::get('', function () {
    dd(returnWithKeyValuesArray(config('countries'), true));
});
