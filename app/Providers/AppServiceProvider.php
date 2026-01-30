<?php

namespace App\Providers;

use App\Models\Cliente;
use App\Models\Orcamento;
use App\Models\Processo;
use App\Models\ContasPagar;
use App\Models\ContasReceber;
use App\Models\Manutencao;
use App\Models\Solicitacao;
use App\Models\Contrato;
use App\Models\Anexo;
use App\Observers\AnexoObserver;
use App\Observers\ClienteObserver;
use App\Observers\OrcamentoObserver;
use App\Observers\ProcessoObserver;
use App\Observers\ContasPagarObserver;
use App\Observers\ContasReceberObserver;
use App\Observers\ManutencaoObserver;
use App\Observers\SolicitacaoObserver;
use App\Observers\ContratoObserver;
use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Carbon::setLocale('pt_BR');

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');

        Cliente::observe(ClienteObserver::class);
        Processo::observe(ProcessoObserver::class);
        ContasPagar::observe(ContasPagarObserver::class);
        ContasReceber::observe(ContasReceberObserver::class);
        Orcamento::observe(OrcamentoObserver::class);
        Manutencao::observe(ManutencaoObserver::class);
        Solicitacao::observe(SolicitacaoObserver::class);
        Contrato::observe(ContratoObserver::class);
        Anexo::observe(AnexoObserver::class);
    }
}
