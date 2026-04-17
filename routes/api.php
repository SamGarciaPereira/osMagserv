<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;

// --- ROTA PRINCIPAL DO WEBHOOK ---
Route::post('/webhook', [WhatsAppWebhookController::class, 'handle']);