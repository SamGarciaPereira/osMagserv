<?php

namespace App\Observers;

use App\Models\Processo;
use App\Models\ContasReceber;

class ProcessoObserver
{
    /**
     * Handle the Processo "created" event.
     */
    public function created(Processo $processo): void
    {
        
    }

    /**
     * Handle the Processo "updated" event.
     */
    public function updated(Processo $processo)
    {
        
    }

    /**
     * Handle the Processo "deleted" event.
     */
    public function deleted(Processo $processo): void
    {
        //
    }

    /**
     * Handle the Processo "restored" event.
     */
    public function restored(Processo $processo): void
    {
        //
    }

    /**
     * Handle the Processo "force deleted" event.
     */
    public function forceDeleted(Processo $processo): void
    {
        //
    }
}
