<?php

use Illuminate\Support\Facades\Route;

Route::fallback(function(){
    return response()->json([
        'message' => 'Method not found.'], 404);
});

Route::group(['namespace' => 'API'], function () {

});
