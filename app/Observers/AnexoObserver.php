<?php

namespace App\Observers;

use App\Models\Anexo;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class AnexoObserver
{
    /**
     * Handle the Anexo "created" event.
     */
    public function created(Anexo $anexo): void
    {
        $this->registerActivity($anexo, 'adicionado');
    }

    /**
     * Handle the Anexo "deleted" event.
     */
    public function deleted(Anexo $anexo): void
    {
        $this->registerActivity($anexo, 'removido');
    }

    protected function registerActivity(Anexo $anexo, string $acao)
    {
        if (!$anexo->anexable) {
            return;
        }

        $parent = $anexo->anexable;

        $lastVersion = Activity::where('subject_type', get_class($parent))
            ->where('subject_id', $parent->id)
            ->max('version');

        $newVersion = $lastVersion ? $lastVersion + 1 : 1;

        $properties = [];

        if ($acao === 'adicionado') {
            $properties = [
                'old' => ['anexo' => null],
                'attributes' => ['anexo' => $anexo->nome_original]
            ];
            $event = 'updated'; 
        } else {
            $properties = [
                'old' => ['anexo' => $anexo->nome_original],
                'attributes' => ['anexo' => null]
            ];
            $event = 'updated';
        }

        Activity::create([
            'user_id'      => Auth::id(),
            'subject_type' => get_class($parent), 
            'subject_id'   => $parent->id,
            'event'        => $event,
            'version'      => $newVersion,
            'properties'   => $properties,
            'description'  => "Anexo {$acao}: {$anexo->nome_original}",
        ]);
        
        $parent->touch(); 
    }
}