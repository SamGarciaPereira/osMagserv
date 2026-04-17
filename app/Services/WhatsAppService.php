<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;

    public function __construct()
    {
        $this->apiUrl = config('services.evolution.url', 'http://127.0.0.1:8081');
    }

    // Helper: Envia mensagem de volta para o usuário via Evolution API
    public function sendMessage(string $instance, string $number, string $text, string $apiKey)
    {
        try {
            return Http::withHeaders(['apiKey' => $apiKey])->post(
                "{$this->apiUrl}/message/sendText/{$instance}",
                [
                    "number" => $number,
                    "options" => ["delay" => 1200, "presence" => "composing"],
                    "text" => $text
                ]
            );
        } catch (\Exception $e) {
            Log::error("Erro Evolution API: " . $e->getMessage());
            return false;
        }
    }

    //Envia o menu principal de serviços (após o login)
    public function sendMainMenu(string $instance, string $sender, string $apiKey, string $clientName)
    {
        $menu = "Olá, *{$clientName}*!\n\nComo podemos te ajudar?\n\n*1)* Solicitação de orçamento\n*2)* Abertura de chamado de manutenção corretiva\n\nDigite 'sair' a qualquer momento para reiniciar.";
        return $this->sendMessage($instance, $sender, $menu, $apiKey);
    }
}