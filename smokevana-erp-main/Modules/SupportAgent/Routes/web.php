<?php

use Illuminate\Support\Facades\Route;
use Modules\SupportAgent\Http\Controllers\SupportAgentController;

/*
|--------------------------------------------------------------------------
| Support Agent Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for the Support Agent module.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group.
|
*/

Route::middleware(['web', 'SetSessionData', 'auth', 'language', 'timezone', 'AdminSidebarMenu'])
    ->prefix('support-agent')
    ->group(function () {
        // Main chat interface
        Route::get('/', [SupportAgentController::class, 'index'])->name('supportagent.index');
        
        // Chat endpoints
        Route::post('/chat', [SupportAgentController::class, 'chat'])->name('supportagent.chat');
        Route::get('/suggestions', [SupportAgentController::class, 'getSuggestions'])->name('supportagent.suggestions');
        Route::post('/clear-history', [SupportAgentController::class, 'clearHistory'])->name('supportagent.clear');
        Route::get('/documentation', [SupportAgentController::class, 'getDocumentation'])->name('supportagent.docs');
        
        // Widget endpoint for embedding
        Route::get('/widget', [SupportAgentController::class, 'widget'])->name('supportagent.widget');
        
        // MCP (Model Context Protocol) endpoints
        Route::get('/mcp/context', [SupportAgentController::class, 'getMcpContext'])->name('supportagent.mcp.context');
        Route::get('/mcp/structure', [SupportAgentController::class, 'getCodebaseStructure'])->name('supportagent.mcp.structure');
    });
