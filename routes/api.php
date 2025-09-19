<?php
use Illuminate\Http\Request;
Route::get('/test', function () {
    return response()->json(['message' => 'Laravel API on Vercel!']);
});
