<?php

use App\Services\GarminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/webhooks/garmin', function (Request $request, GarminService $garminService) {
    $garminService->handleWebhook($request->all());
    return response()->json(['status' => 'ok']);
});
