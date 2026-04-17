<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;
use App\Models\Cliente;
use App\Models\Solicitacao;
use Carbon\Carbon;

class WhatsAppWebhookController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    // --- ROTA PRINCIPAL DO WEBHOOK ---
    public function handle(Request $request)
    {
        $apiKey = env('EVOLUTION_API_KEY'); 

        if (!$request->has('data.message.conversation')) {
            return response()->json(['status' => 'ok', 'message' => 'Not a text message, ignored.']);
        }
        $data = $request->input('data');
        if ($data['key']['fromMe'] === true) {
            return response()->json(['status' => 'ok', 'message' => 'Message from self, ignored.']);
        }

        $instanceName = $request->input('instance');
        $sender = $data['key']['remoteJid'];
        $message = trim($request->input('data.message.conversation'));

        // Lógica global de cancelamento
        if (strtolower($message) === 'sair') {
            Cache::forget('conversation_' . $sender); // Limpa o estado da conversa
            $this->whatsappService->sendMessage($instanceName, $sender, "Atendimento cancelado. Para começar de novo, envie qualquer mensagem.", $apiKey);
            return response()->json(['status' => 'ok']);
        }
        
        // --- Controle da Máquina de Estados ---
        $conversation = Cache::get('conversation_' . $sender);
        $state = $conversation['state'] ?? null;

        // Roteador de estados da conversa
        switch ($state) {
            
            case 'awaiting_client_type_choice':
                $choice = preg_replace('/[^1-2]/', '', $message);
                
                if ($choice == '1') { // Já é cliente
                    $conversation['state'] = 'awaiting_existing_client_lookup';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMessage($instanceName, $sender, "Entendido. Por favor, informe seu *CNPJ ou CPF* para localizarmos seu cadastro:", $apiKey);
                
                } elseif ($choice == '2') { // Não é cliente
                    $conversation['state'] = 'register_awaiting_name';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMessage($instanceName, $sender, "Vamos iniciar seu cadastro. Por favor, informe o *nome da empresa* (Razão Social):", $apiKey);
                
                } else {
                    $cleanedDocumento = preg_replace('/[^0-9]/', '', $message);
                    
                    if (strlen($cleanedDocumento) == 11 || strlen($cleanedDocumento) == 14) {
                        
                        $cliente = Cliente::whereRaw('REGEXP_REPLACE(documento, "[^0-9]", "") = ?', [$cleanedDocumento])->first();
                        
                        if ($cliente) {
                            $conversation['data']['cliente_id'] = $cliente->id;
                            $conversation['data']['cliente_nome'] = $cliente->nome;
                            $conversation['state'] = 'awaiting_main_menu_choice';
                            Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                            $this->whatsappService->sendMainMenu($instanceName, $sender, $apiKey, $cliente->nome);
                        } else {
                            $conversation['state'] = 'register_awaiting_name'; 
                            Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                            $this->whatsappService->sendMessage($instanceName, $sender, "Não localizamos seu cadastro com este documento. Vamos iniciar um novo.\n\nPor favor, informe o *nome da empresa* (Razão Social):", $apiKey);
                        }
                    } else {
                        $this->whatsappService->sendMessage($instanceName, $sender, "Opção inválida. Digite *1* (Já sou cliente) ou *2* (Ainda não sou cliente).", $apiKey);
                        Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    }
                }
                break;

            // Estado: Busca cliente por CNPJ/CPF
            case 'awaiting_existing_client_lookup':
                $cleanedDocumento = preg_replace('/[^0-9]/', '', $message);
                $cliente = Cliente::whereRaw('REGEXP_REPLACE(documento, "[^0-9]", "") = ?', [$cleanedDocumento])->first();
                
                if ($cliente) {
                    $conversation['data']['cliente_id'] = $cliente->id;
                    $conversation['data']['cliente_nome'] = $cliente->nome;
                    $conversation['state'] = 'awaiting_main_menu_choice';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMainMenu($instanceName, $sender, $apiKey, $cliente->nome);
                } else {
                    $conversation['state'] = 'register_awaiting_name'; 
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMessage($instanceName, $sender, "Não localizamos seu cadastro. Vamos iniciar um novo.\n\nPor favor, informe o *nome da empresa* (Razão Social):", $apiKey);
                }
                break;

            // --- Início do Fluxo de Cadastro ---
            case 'register_awaiting_name':
                $conversation['data']['nome'] = $message;
                $conversation['state'] = 'register_awaiting_responsavel_name';
                Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                $this->whatsappService->sendMessage($instanceName, $sender, "Obrigado. Agora, informe o *nome do responsável* pela empresa:", $apiKey);
                break;

            case 'register_awaiting_responsavel_name':
                $conversation['data']['responsavel'] = $message;
                $conversation['state'] = 'register_awaiting_email';
                Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                $this->whatsappService->sendMessage($instanceName, $sender, "Qual o *e-mail* principal para contato?", $apiKey);
                break;
        
            case 'register_awaiting_email':
                if (!filter_var($message, FILTER_VALIDATE_EMAIL)) {
                     $this->whatsappService->sendMessage($instanceName, $sender, "E-mail inválido. Por favor, insira um e-mail válido:", $apiKey);
                     Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                     break; 
                }
                $conversation['data']['email'] = $message;
                $conversation['state'] = 'register_awaiting_document';
                Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                $this->whatsappService->sendMessage($instanceName, $sender, "Para finalizar, qual o *CNPJ ou CPF* da empresa?", $apiKey);
                break;

            // Estado: Salva o novo cliente no DB
            case 'register_awaiting_document':
                $conversation['data']['documento'] = preg_replace('/[^0-9]/', '', $message);
                
                try {
                    $cleanedNumber = $this->cleanWhatsAppNumber($sender);

                    $cliente = Cliente::create([
                        'nome' => $conversation['data']['nome'],
                        'responsavel' => $conversation['data']['responsavel'],
                        'email' => $conversation['data']['email'],
                        'documento' => $conversation['data']['documento'],
                        'telefone' => preg_replace('/@s\.whatsapp\.net$/', '', $sender),
                    ]);

                    // "Loga" o cliente e vai para o menu de serviços
                    $conversation['data']['cliente_id'] = $cliente->id;
                    $conversation['data']['cliente_nome'] = $cliente->nome;
                    $conversation['state'] = 'awaiting_main_menu_choice';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    
                    Log::info('Novo cliente cadastrado via WhatsApp:', $cliente->toArray());
                    $this->whatsappService->sendMessage($instanceName, $sender, "Cadastro realizado com sucesso!", $apiKey);
                    $this->whatsappService->sendMainMenu($instanceName, $sender, $apiKey, $cliente->nome);

                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) { // Erro de duplicidade (CNPJ/Email)
                        $this->whatsappService->sendMessage($instanceName, $sender, "Este CNPJ/CPF ou E-mail já está cadastrado. Por favor, digite 'sair' e reinicie a conversa selecionando 'Já sou cliente'.", $apiKey);
                    } else {
                        Log::error("Erro ao cadastrar cliente via WhatsApp: " . $e->getMessage(), $conversation['data']);
                        $this->whatsappService->sendMessage($instanceName, $sender, "❌ Ocorreu um erro ao registrar seu cadastro. Por favor, tente novamente.", $apiKey);
                    }
                    Cache::forget('conversation_' . $sender);
                }
                break;

            // --- NÍVEL 2: Menu de Serviços (Pós-Login) ---
            case 'awaiting_main_menu_choice':
                $choice = preg_replace('/[^1-2]/', '', $message);
                if ($choice == '1') { // 1. Orçamento
                    $conversation['state'] = 'orcamento_awaiting_description';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMessage($instanceName, $sender, "Você selecionou *Solicitação de Orçamento*.\n\nPor favor, *descreva sua solicitação*:", $apiKey);
                    
                } elseif ($choice == '2') { // 2. Manutenção
                    
                    $clienteId = $conversation['data']['cliente_id'] ?? null;
                    $cliente = null;

                    if ($clienteId) {
                        $cliente = Cliente::find($clienteId);
                    }
                    
                    // Extrai o contrato para uma variável
                    $contrato = $cliente ? $cliente->contratoAtivo() : null;

                    Log::info('Checagem de contrato WhatsApp:', [
                        'cliente_id' => $clienteId,
                        'contrato_encontrado' => $contrato ? 'Sim' : 'Nao'
                    ]);

                    // Verificação estrita: o cliente e o contrato não podem ser nulos
                    if ($cliente !== null && $contrato !== null) {
                        
                        $conversation['state'] = 'manutencao_awaiting_area'; 
                        Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                        $this->whatsappService->sendMessage($instanceName, $sender, "Você selecionou *Abertura de Chamado Corretivo*.\n\nPor favor, *selecione a área de atuação* do problema:\n1- Civil\n2- Hidráulica\n3- Elétrica", $apiKey);
                    
                    } else {
                        // Mensagem de bloqueio
                        $this->whatsappService->sendMessage($instanceName, $sender, "⚠️ Desculpe, não localizamos um *contrato de manutenção ativo* vinculado ao seu cadastro.\n\nPara prosseguir com um chamado de manutenção, é necessário possuir um contrato vigente. Você pode selecionar a opção *1* para solicitar um *Orçamento avulso* ou digitar 'sair' para encerrar.", $apiKey);
                        Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    }
                
                } else {
                    $this->whatsappService->sendMessage($instanceName, $sender, "Opção inválida. Digite *1* (Orçamento) ou *2* (Manutenção).", $apiKey);
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                }
                break;
            
            // --- (Orçamento -> Solicitação) ---
            case 'orcamento_awaiting_description':
                $conversation['data']['escopo'] = $message;
                try {
                    // LÓGICA DE SOLICITAÇÃO
                    Solicitacao::create([
                        'tipo' => 'orcamento',
                        'status' => 'Pendente',
                        'data_solicitacao' => Carbon::now(), 
                        'dados' => [
                            'cliente_id' => $conversation['data']['cliente_id'],
                            'cliente_nome' => $conversation['data']['cliente_nome'], 
                            'escopo' => $conversation['data']['escopo'],
                            'status' => 'Pendente', 
                        ]
                    ]);

                    Log::info('Nova SOLICITAÇÃO DE ORÇAMENTO criada via WhatsApp.');

                    $successMessage = "✅ *Solicitação de orçamento registrada com sucesso* para *{$conversation['data']['cliente_nome']}*!\n\n";
                    $successMessage .= "*Descrição:* {$conversation['data']['escopo']}\n\n";
                    $successMessage .= "Nossa equipe irá analisar sua solicitação e retornará em breve.\n\nFIM.";

                    $this->whatsappService->sendMessage($instanceName, $sender, $successMessage, $apiKey);
                    Cache::forget('conversation_' . $sender);

                } catch (\Exception $e) {
                    Log::error("Erro ao criar SOLICITAÇÃO DE ORÇAMENTO via WhatsApp: " . $e->getMessage(), $conversation['data']);
                    $this->whatsappService->sendMessage($instanceName, $sender, "❌ Ocorreu um erro ao registrar sua solicitação.", $apiKey);
                    Cache::forget('conversation_' . $sender);
                }
                break;

            // --- Início do Fluxo de Manutenção ---

            case 'manutencao_awaiting_area':
                $areas = ['1' => 'Civil', '2' => 'Hidráulica', '3' => 'Elétrica'];
                $choice = preg_replace('/[^1-3]/', '', $message);

                if (array_key_exists($choice, $areas)) {
                    $conversation['data']['area'] = $areas[$choice];
                    $conversation['state'] = 'manutencao_awaiting_requester';
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                    $this->whatsappService->sendMessage($instanceName, $sender, "Entendido. Agora, por favor, informe o *nome do solicitante*:", $apiKey);
                } else {
                    $this->whatsappService->sendMessage($instanceName, $sender, "Opção inválida. Por favor, digite 1, 2 ou 3 para a área.", $apiKey);
                    Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10));
                }
                break;

            case 'manutencao_awaiting_requester':
                $conversation['data']['solicitante'] = $message;
                $conversation['state'] = 'manutencao_awaiting_description';
                Cache::put('conversation_' . $sender, $conversation, now()->addMinutes(10)); 
                $this->whatsappService->sendMessage($instanceName, $sender, "Obrigado, {$message}.\n\nPara finalizar, *descreva o problema* que você está enfrentando:", $apiKey);
                break;
                
            case 'manutencao_awaiting_description':
                $conversation['data']['descricao'] = $message;
                
                try {
                    Solicitacao::create([
                        'tipo' => 'manutencao_corretiva',
                        'status' => 'Pendente',
                        'data_solicitacao' => Carbon::now(), 
                        'dados' => [
                            'cliente_id' => $conversation['data']['cliente_id'],
                            'cliente_nome' => $conversation['data']['cliente_nome'],
                            'area' => $conversation['data']['area'],
                            'descricao' => $conversation['data']['descricao'],
                            'solicitante' => $conversation['data']['solicitante'],
                            'tipo' => 'Corretiva', 
                            'status' => 'Pendente', 
                        ]
                    ]);

                    Log::info('Nova SOLICITAÇÃO DE MANUTENÇÃO corretiva criada via WhatsApp.');

                    // Mensagem de sucesso com a "Área"
                    $successMessage = "✅ *Solicitação de chamado registrada com sucesso* para *{$conversation['data']['cliente_nome']}*!\n\n";
                    $successMessage .= "*Área:* {$conversation['data']['area']}\n";
                    $successMessage .= "*Solicitante:* {$conversation['data']['solicitante']}\n";
                    $successMessage .= "*Problema:* {$conversation['data']['descricao']}\n\n";
                    $successMessage .= "Nossa equipe irá analisar sua solicitação e retornará em breve.\n\nFIM.";

                    $this->whatsappService->sendMessage($instanceName, $sender, $successMessage, $apiKey);
                    Cache::forget('conversation_' . $sender);

                } catch (\Exception $e) {
                    Log::error("Erro ao criar SOLICITAÇÃO DE MANUTENÇÃO via WhatsApp: " . $e->getMessage(), $conversation['data']);
                    $this->whatsappService->sendMessage($instanceName, $sender, "❌ Ocorreu um erro ao registrar seu chamado.", $apiKey);
                    Cache::forget('conversation_' . $sender);
                }
                break;
                
            // Estado Padrão: Início da conversa
            default:
                $menu = "Bem vindo ao autoatendimento da MAGSERV!\n\nVocê já é nosso cliente?\n\n*1)* Sim, já sou cliente\n*2)* Não, ainda não sou cliente\n\nDigite 'sair' a qualquer momento para reiniciar.";
                $this->whatsappService->sendMessage($instanceName, $sender, $menu, $apiKey);
                Cache::put('conversation_' . $sender, ['state' => 'awaiting_client_type_choice', 'data' => []], now()->addMinutes(10));
                break;
        }

        // Responde 200 OK para a API da Evolution
        return response()->json(['status' => 'ok']);
    }

    //Limpa o número do WhatsApp
    private function cleanWhatsAppNumber(string $senderNumber) {
        $number = preg_replace('/@s\.whatsapp\.net$/', '', $senderNumber);
        
        if (str_starts_with($number, '55')) {
            $number = substr($number, 2);
        }

        if (strlen($number) == 10 || strlen($number) == 11) {
            $ddd = substr($number, 0, 2);
            $restante = substr($number, 2);
            return $ddd . ' ' . $restante; 
        }
        
        return $number; 
    }
}