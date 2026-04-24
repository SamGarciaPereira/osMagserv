<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\index\DashboardController;
use App\Http\Controllers\cliente\ClienteController;
use App\Http\Controllers\orcamento\OrcamentoController;
use App\Http\Controllers\processo\ProcessoController;
use App\Http\Controllers\manutencao\ManutencaoController;
use App\Http\Controllers\financeiro\ContasPagarController;
use App\Http\Controllers\financeiro\ContasReceberController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\anexo\AnexoController;
use App\Http\Controllers\contrato\ContratoController;
use App\Http\Controllers\rh\FuncionarioController;
use App\Http\Controllers\admin\SolicitacaoController;

// ==========================================
// ROTAS DE AUTENTICAÇÃO
// ==========================================
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function() {
    
    // ROTA PRINCIPAL (DASHBOARD)
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    // 1. ROTAS ADMIN (Acesso Exclusivo)
    Route::middleware(['admin'])->group(function () {
        // MÓDULO FINANCEIRO 
        Route::prefix('financeiro')->name('financeiro.')->group(function () {
            Route::resource('contas-pagar', ContasPagarController::class);
            Route::resource('contas-receber', ContasReceberController::class);
        });
        // MÓDULO DE RH
        Route::prefix('rh')->name('rh.')->group(function () {
            Route::resource('funcionarios', FuncionarioController::class);
        });
    });

    // 2. ROTAS COMPARTILHADAS (Supervisor SÓ PODE LER - Index/Show)
    
    Route::middleware([\App\Http\Middleware\OrcamentistaMiddleware::class])->group(function () {
        Route::resource('processos', ProcessoController::class)->only(['index', 'edit', 'update', 'show']);
    });

    // Manutenções Genéricas: Supervisor só acede à listagem
    Route::resource('manutencoes', ManutencaoController::class)
        ->parameters(['manutencoes' => 'manutencao'])
        ->only(['index']);

    // Manutenção Corretiva: Supervisor só vê a listagem
    Route::prefix('manutencoes/corretiva')->name('manutencoes.corretiva.')->group(function () {
        Route::get('/', [ManutencaoController::class, 'indexCorretiva'])->name('index');
    });

    // Manutenção Preventiva: Supervisor só vê a listagem
    Route::prefix('manutencoes/preventiva')->name('manutencoes.preventiva.')->group(function () {
        Route::get('/', [ManutencaoController::class, 'indexPreventiva'])->name('index');
    });

    // Anexos: Públicos para todos os utilizadores logados (para poderem anexar a processos/manutenções)
    Route::post('/anexos/upload', [AnexoController::class, 'store'])->name('anexos.store');
    Route::delete('/anexos/{anexo}', [AnexoController::class, 'destroy'])->name('anexos.destroy');
    Route::get('/anexos/{anexo}/download', [AnexoController::class, 'download'])->name('anexos.download');
    Route::get('/anexos/{anexo}/{filename}', [AnexoController::class, 'show'])->name('anexos.show');


    // 3. ROTAS RESTRITAS (Bloqueadas para o Supervisor)
    Route::middleware([\App\Http\Middleware\SupervisorMiddleware::class])->group(function () {
        
        // Módulos inteiros que o supervisor não pode ver nem mexer
        Route::resource('clientes', ClienteController::class);
        Route::resource('orcamentos', OrcamentoController::class);
        Route::resource('contratos', ContratoController::class);
        
        // Processos: Tudo menos index e show fica bloqueado. Orçamentista bloqueado.
        Route::middleware([\App\Http\Middleware\BloqueiaProcessosOrcamentista::class])->group(function () {
            Route::resource('processos', ProcessoController::class)->except(['index', 'edit', 'update', 'show']);
        });
        
        // Manutenções: Tudo MENOS index fica bloqueado
        Route::resource('manutencoes', ManutencaoController::class)
            ->parameters(['manutencoes' => 'manutencao'])
            ->except(['index']);

        // Manutenções Específicas: Create e Edit bloqueados
        Route::get('manutencoes/corretiva/create', [ManutencaoController::class, 'createCorretiva'])->name('manutencoes.corretiva.create');
        Route::get('manutencoes/corretiva/{manutencao}/edit', [ManutencaoController::class, 'editCorretiva'])->name('manutencoes.corretiva.edit');
        
        Route::get('manutencoes/preventiva/create', [ManutencaoController::class, 'createPreventiva'])->name('manutencoes.preventiva.create');
        Route::get('manutencoes/preventiva/{manutencao}/edit', [ManutencaoController::class, 'editPreventiva'])->name('manutencoes.preventiva.edit');

        // Solicitações: O supervisor não aprova nem recusa solicitações
        Route::get('admin/solicitacoes', [SolicitacaoController::class, 'index'])->name('admin.solicitacao.index');
        Route::post('admin/solicitacoes/{solicitacao}/aceitar', [SolicitacaoController::class, 'accept'])->name('admin.solicitacoes.accept');
        Route::post('admin/solicitacoes/{solicitacao}/recusar', [SolicitacaoController::class, 'reject'])->name('admin.solicitacoes.reject');
        
    });
});