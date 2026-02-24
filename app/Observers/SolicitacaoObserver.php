<?php

namespace App\Observers;

use App\Models\Activity; 
use App\Models\Solicitacao;

class SolicitacaoObserver
{
    /**
     * Handle the Solicitacao "created" event.
     */
    public function created(Solicitacao $solicitacao): void
    {
        //
    }

    public function updated(Solicitacao $solicitacao): void
    {
        //
    }

    /**
     * Handle the Solicitacao "deleted" event.
     */
    public function deleted(Solicitacao $solicitacao): void
    {
        //
    }

    /**
     * Handle the Solicitacao "restored" event.
     */
    public function restored(Solicitacao $solicitacao): void
    {
        //
    }

    /**
     * Handle the Solicitacao "force deleted" event.
     */
    public function forceDeleted(Solicitacao $solicitacao): void
    {
        //
    }
}