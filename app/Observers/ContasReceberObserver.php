<?php

namespace App\Observers;

use App\Models\ContasReceber;
use App\Models\Processo;
use App\Models\Orcamento;

class ContasReceberObserver
{
    /**
     * Handle the ContasReceber "created" event.
     */
    public function created(ContasReceber $contasReceber): void
    {
        if ($contasReceber->processo_id) {
            $contasReceber->load('processo.orcamento');

            if ($contasReceber->processo && $contasReceber->processo->orcamento) {
                $orcamento = $contasReceber->processo->orcamento;
                
                $identificador = $contasReceber->nf 
                    ? "da NF {$contasReceber->nf}" 
                    : "do orçamento {$orcamento->numero_proposta}";
            }
        }  
    }

    /**
     * Handle the ContasReceber "updated" event.
     */
    public function updated(ContasReceber $contasReceber): void
    {
        $contasReceber->load('processo.orcamento');
    }

    /**
     * Handle the ContasReceber "deleted" event.
     */
    public function deleted(ContasReceber $contasReceber): void
    {
        $contasReceber->load('processo.orcamento');
    }

    /**
     * Handle the ContasReceber "restored" event.
     */
    public function restored(ContasReceber $contasReceber): void
    {
        //
    }

    /**
     * Handle the ContasReceber "force deleted" event.
     */
    public function forceDeleted(ContasReceber $contasReceber): void
    {
        //
    }
}
