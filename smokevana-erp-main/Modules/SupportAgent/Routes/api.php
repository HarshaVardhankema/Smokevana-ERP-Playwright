<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportAgent\Http\Controllers\SupportAgentController;

/*
|--------------------------------------------------------------------------
| Support Agent API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for the Support Agent module.
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::middleware(['auth:api'])->group(function () {
    // Chat API endpoint
    Route::post('/chat', [SupportAgentController::class, 'chat']);
    
    // Get suggestions
    Route::get('/suggestions', [SupportAgentController::class, 'getSuggestions']);
    
    // Get documentation
    Route::get('/documentation', [SupportAgentController::class, 'getDocumentation']);
    
    // Widget data
    Route::get('/widget', [SupportAgentController::class, 'widget']);
});
