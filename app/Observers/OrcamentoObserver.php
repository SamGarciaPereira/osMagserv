<?php

namespace App\Observers;

use App\Models\Activity;
use App\Models\Orcamento;
use App\Models\Processo;
use App\Services\CodeGeneratorService;
use Carbon\Carbon;


class OrcamentoObserver
{

   public function creating(Orcamento $orcamento): void
   {
        if (!empty($orcamento->numero_proposta)) {
            return;
        }

        $generator = new CodeGeneratorService();
        $cliente = $orcamento->cliente ?? \App\Models\Cliente::find($orcamento->cliente_id);

        $dataReferencia = $orcamento->data_solicitacao
            ? Carbon::parse($orcamento->data_solicitacao)
            : Carbon::now();

        $orcamento->numero_proposta = $generator->gerarCodigoOrcamento(
            $cliente,
            $orcamento->numero_manual,
            $dataReferencia,
            $orcamento->uf_obra 
        );
    }

    /**
     * Handle the Orcamento "created" event.
     */
    public function created(Orcamento $orcamento): void
    {
        Activity::create([
            'description' => "Orçamento '{$orcamento->numero_proposta}' foi cadastrado."
        ]);

        if($orcamento->status === 'Aprovado') {
            Processo::create([
                'orcamento_id' => $orcamento->id,
                'status' => 'Em Aberto',
            ]);
        }
    }

    /**
     * Handle the Orcamento "updated" event.
     */
    public function updated(Orcamento $orcamento): void
    {
        Activity::create([
            'description' => "Orçamento '{$orcamento->numero_proposta}' foi atualizado."
        ]);

        if($orcamento->isDirty('status') && $orcamento->status === 'Aprovado') {
            Processo::create([
                'orcamento_id' => $orcamento->id,
                'status' => 'Em Aberto',
            ]);
        }
        if($orcamento->processo){
            $orcamento->processo->touch();
        }
    }

    /**
     * Handle the Orcamento "deleted" event.
     */
    public function deleted(Orcamento $orcamento): void
    {
        Activity::create([
            'description' => "Orçamento '{$orcamento->numero_proposta}' foi removido."
        ]);
    }

    /**
     * Handle the Orcamento "restored" event.
     */
    public function restored(Orcamento $orcamento): void
    {
        //
    }

    /**
     * Handle the Orcamento "force deleted" event.
     */
    public function forceDeleted(Orcamento $orcamento): void
    {
        //
    }
}
