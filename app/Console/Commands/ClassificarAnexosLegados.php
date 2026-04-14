<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Anexo;
use Illuminate\Support\Str;

class ClassificarAnexosLegados extends Command
{
    // comando pra rodar direto no terminal 
    protected $signature = 'anexos:classificar-legados';

    // a descrição do que ele faz
    protected $description = 'Varre os anexos antigos do sistema e marca notas e propostas como confidenciais.';

    public function handle()
    {
        // palavras-chave que indicam que o arquivo é financeiro/sensível
        $palavrasSensiveis = [
            'nf', 'nfe', 'danfe', 'nota', 'fiscal', 
            'proposta', 'orcamento', 'orçamento', 
            'boleto', 'comprovante', 'recibo', 'fatura',
            'medição', 'contrato' , 'termo', 'quitação',
            'faturamento', 'autorização', 'art', 
            '410690222141073890', 'entrada' , 'prop.',
        ];

        $this->info('Iniciando a varredura de arquivos antigos...');
        
        $totalAnalisados = 0;
        $totalAtualizados = 0;

        // chunk(200) para não estourar a memória RAM do servidor 
        Anexo::chunk(200, function ($anexos) use ($palavrasSensiveis, &$totalAnalisados, &$totalAtualizados) {
            foreach ($anexos as $anexo) {
                $totalAnalisados++;
                $nomeMinusculo = strtolower($anexo->nome_original);

                // se o nome do arquivo contiver alguma das palavras, nós o marcamos
                if (Str::contains($nomeMinusculo, $palavrasSensiveis)) {
                    $anexo->is_confidencial = true;
                    $anexo->save();
                    
                    $totalAtualizados++;
                    $this->line("Ocultado: " . $anexo->nome_original); 
                }
            }
        });

        $this->info("Varredura concluída!");
        $this->info("Total de arquivos analisados: {$totalAnalisados}");
        $this->info("Total marcados como confidenciais: {$totalAtualizados}");
        
        return 0;
    }
}