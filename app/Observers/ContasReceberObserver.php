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
         
    }

    /**
     * Handle the ContasReceber "updated" event.
     */
    public function updated(ContasReceber $contasReceber): void
    {
        
    }

    /**
     * Handle the ContasReceber "deleted" event.
     */
    public function deleted(ContasReceber $contasReceber): void
    {
        
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
