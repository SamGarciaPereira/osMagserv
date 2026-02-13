<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Funcionario;
use Carbon\Carbon;

class VerificarDocumentosFuncionarios extends Command
{
    protected $signature = 'rh:verificar-documentos';
    protected $description = 'Atualiza o status dos documentos (Em dia, Atencao, Vencido)';

    public function handle()
    {
        $this->info('Iniciando verificação de documentos...');
        
        $funcionarios = Funcionario::where('ativo', true)
            ->whereIn('tipo_contrato', ['Fixo', 'Intermitente'])
            ->get();

        $hoje = Carbon::now()->startOfDay();
        $countAlterados = 0;

        foreach ($funcionarios as $funcionario) {
            $novoStatus = 'Em dia'; 

            $regras = [
                'doc_aso' => 1,
                'doc_ordem_servico' => 1,
                'doc_ficha_epi' => 1,
                'doc_nr06' => 1,
                'doc_nr12' => 1,
                'doc_nr10' => 2,
                'doc_nr18' => 2,
                'doc_nr35' => 2,
            ];

            if ($funcionario->tipo_contrato === 'Intermitente') {
                $regras['doc_contrato_intermitente'] = 1;
            }

            foreach ($regras as $campo => $anos) {
                if (!$funcionario->$campo) continue;

                $dataEmissao = Carbon::parse($funcionario->$campo);
                $vencimento = $dataEmissao->copy()->addYears($anos)->startOfDay();
                
                $dataAlerta = $vencimento->copy()->subMonth();

                if ($hoje->gt($vencimento)) {
                    $novoStatus = 'Vencido';
                    break; 
                }

                if ($hoje->gte($dataAlerta) && $novoStatus === 'Em dia') {
                    $novoStatus = 'Atencao';
                }
            }

            if ($funcionario->status_documentos !== $novoStatus) {
                $funcionario->update(['status_documentos' => $novoStatus]);
                $countAlterados++;
            }
        }

        $this->info("Verificação concluída! {$countAlterados} funcionários atualizados.");
    }
}