<?php

namespace App\Services;

use App\Models\Manutencao;
use App\Models\Contrato;
use App\Models\Orcamento;
use App\Models\Cliente;
use Carbon\Carbon;

class CodeGeneratorService
{
    protected $base = '01'; 

    /**
     * Gera código para Manutenção (Preventiva ou Corretiva)
     */
    public function gerarCodigoManutencao(Cliente $cliente, string $tipo)
    {
        $now = Carbon::now();
        $anoMes = $now->format('my'); 
        
        $estado = !empty($cliente->estado) ? strtoupper($cliente->estado) : 'PR';
        
        $tipoLetra = ($tipo === 'Preventiva') ? 'P' : 'C';

        $idClienteStr = str_pad($cliente->id, 3, '0', STR_PAD_LEFT);

        $countGeral = Manutencao::where('tipo', $tipo)->count() + 1;
        $geralStr = str_pad($countGeral, 3, '0', STR_PAD_LEFT);

        $countClienteMes = Manutencao::where('cliente_id', $cliente->id)
            ->where('tipo', $tipo)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count() + 1;
        $ocorrenciaStr = str_pad($countClienteMes, 2, '0', STR_PAD_LEFT);

        return "{$estado}-{$this->base}-{$anoMes}-{$tipoLetra}-{$idClienteStr}-{$geralStr}-{$ocorrenciaStr}";
    }

    /**
     * Formata a string do código (Apenas visual)
     */
    public function formatarCodigoOrcamento(Cliente $cliente, Carbon $data, int $sequencial, ?string $ufObra = null)
{
    $anoMes = $data->format('my');
    
    if (!empty($ufObra)) {
        $estado = strtoupper($ufObra);
    } else {
        $estado = !empty($cliente->estado) ? strtoupper($cliente->estado) : 'PR';
    }

    $sequenciaStr = str_pad($sequencial, 3, '0', STR_PAD_LEFT);

    return "{$estado}-{$this->base}-{$anoMes}-O-{$sequenciaStr}";
}

    /**
     * Gera o código Sequencial Global por Ano
     */
    public function gerarCodigoOrcamento(Cliente $cliente = null, ?int $numeroManual = null, ?Carbon $dataReferencia = null, ?string $ufOverride = null)
    {
        $dataBase = $dataReferencia ?? Carbon::now();
        
        $cliente = $cliente ?? new Cliente(['estado' => 'PR']);

        if ($numeroManual !== null) {
            return $this->formatarCodigoOrcamento($cliente, $dataBase, $numeroManual, $ufOverride);
        }

        $anoShort = $dataBase->format('y');

        $padraoBusca = "%-{$this->base}-%{$anoShort}-O-%";
        
        $orcamentosDoAno = Orcamento::where('numero_proposta', 'LIKE', $padraoBusca)
                                    ->pluck('numero_proposta');

        $maxSequencial = 0;

        foreach ($orcamentosDoAno as $codigo) {
            $partes = explode('-', $codigo);
            $ultimoSegmento = end($partes);

            if (is_numeric($ultimoSegmento)) {
                $seq = (int) $ultimoSegmento;
                if ($seq > $maxSequencial) {
                    $maxSequencial = $seq;
                }
            }
        }

        $proximoSequencial = $maxSequencial + 1;

        $codigoFinal = $this->formatarCodigoOrcamento($cliente, $dataBase, $proximoSequencial, $ufOverride);

        while (Orcamento::where('numero_proposta', $codigoFinal)->exists()) {
            $proximoSequencial++;
            $codigoFinal = $this->formatarCodigoOrcamento($cliente, $dataBase, $proximoSequencial, $ufOverride);
        }

        return $codigoFinal;
    }

    public function gerarCodigoContrato(Cliente $cliente)
    {
        $now = Carbon::now();
        $anoVigente = $now->format('Y');
        $estado = !empty($cliente->estado) ? strtoupper($cliente->estado) : 'PR';

        $idClienteStr = str_pad($cliente->id, 3, '0', STR_PAD_LEFT);

        $countContrato = Contrato::count() + 1;
        $idContratoStr = str_pad($countContrato, 3, '0', STR_PAD_LEFT);

        return "{$estado}-{$idClienteStr}-CT{$idContratoStr}-{$anoVigente}";
    }
}