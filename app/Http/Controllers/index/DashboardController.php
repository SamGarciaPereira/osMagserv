<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Activity;
use App\Models\Processo;
use App\Models\Orcamento;
use App\Models\Manutencao;
use App\Models\ContasReceber;
use App\Models\ContasPagar;
use App\Models\Solicitacao;
use App\Models\Contrato;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $inputInicio = $request->input('data_inicio', now()->format('Y-m'));
        $inputFim    = $request->input('data_fim');

        try {
            $dataInicio = Carbon::parse($inputInicio)->startOfMonth();
        } catch (\Exception $e) {
            $dataInicio = now()->startOfMonth();
            $inputInicio = $dataInicio->format('Y-m');
        }

        if ($inputFim) {
            try {
                $dataFim = Carbon::parse($inputFim)->endOfMonth();
                
                if ($dataFim->lt($dataInicio)) {
                    $dataFim = $dataInicio->copy()->endOfMonth();
                    $inputFim = null;
                }
            } catch (\Exception $e) {
                $dataFim = $dataInicio->copy()->endOfMonth();
                $inputFim = null;
            }
        } else {
            $dataFim = $dataInicio->copy()->endOfMonth();
        }
        
        $getStats = function($query) {
            return $query->groupBy('status')
                         ->pluck(DB::raw('count(*) as total'), 'status')
                         ->toArray();
        };
        
        $getSumStats = function($query) {
            return $query->groupBy('status')
                         ->pluck(DB::raw('sum(valor) as total'), 'status')
                         ->toArray();
        };

        $processosStats = $getStats(Processo::whereBetween('created_at', [$dataInicio, $dataFim]));

        $orcamentosStats = $getStats(Orcamento::where(function($query) use ($dataInicio, $dataFim) {
            $query->where(function($q) use ($dataInicio, $dataFim) {
                $q->where('status', 'Pendente')
                  ->where(function($sq) use ($dataInicio, $dataFim) {
                      $sq->whereBetween('data_solicitacao', [$dataInicio, $dataFim])
                         ->orWhere(function($fq) use ($dataInicio, $dataFim) {
                             $fq->whereNull('data_solicitacao')
                                ->whereBetween('created_at', [$dataInicio, $dataFim]);
                         });
                  });
            })
            ->orWhere(function($q) use ($dataInicio, $dataFim) {
                $q->whereIn('status', ['Em Andamento', 'Em Validação', 'Validado'])
                  ->whereBetween('updated_at', [$dataInicio, $dataFim]);
            })
            ->orWhere(function($q) use ($dataInicio, $dataFim) {
                $q->where('status', 'Enviado')
                  ->whereBetween('data_envio', [$dataInicio, $dataFim]);
            })
            ->orWhere(function($q) use ($dataInicio, $dataFim) {
                $q->where('status', 'Aprovado')
                  ->whereBetween('data_aprovacao', [$dataInicio, $dataFim]);
            });
        }));
        
        $prevStats = $getStats(Manutencao::where('tipo', 'Preventiva')
            ->where(function($query) use ($dataInicio, $dataFim) {
                $query->where(function($q) use ($dataInicio, $dataFim) {
                    $q->whereNotNull('data_inicio_atendimento')
                      ->whereBetween('data_inicio_atendimento', [$dataInicio, $dataFim]);
                })->orWhere(function($q) use ($dataInicio, $dataFim) {
                    $q->whereNull('data_inicio_atendimento')
                      ->whereBetween('created_at', [$dataInicio, $dataFim]);
                });
            }));

        $corrStats = $getStats(Manutencao::where('tipo', 'Corretiva')
            ->where(function($query) use ($dataInicio, $dataFim) {
                $query->where(function($q) use ($dataInicio, $dataFim) {
                    $q->whereNotNull('data_inicio_atendimento')
                      ->whereBetween('data_inicio_atendimento', [$dataInicio, $dataFim]);
                })->orWhere(function($q) use ($dataInicio, $dataFim) {
                    $q->whereNull('data_inicio_atendimento')
                      ->whereBetween('created_at', [$dataInicio, $dataFim]);
                });
            }));

        $totalContratos = Contrato::count();

        $ativosCount = Contrato::where('ativo', true)
            ->whereDate('data_inicio', '<=', $dataFim)
            ->whereDate('data_fim', '>=', $dataInicio)
            ->count();

        $inativosCount = $totalContratos - $ativosCount;
        if ($inativosCount < 0) $inativosCount = 0;

        $contratosStats = array_filter([
            'Ativo'   => $ativosCount,
            'Inativo' => $inativosCount
        ], fn($valor) => $valor > 0);

        $receberStats = $getSumStats(ContasReceber::whereBetween('data_vencimento', [$dataInicio, $dataFim]));
        
        $pagarStats = $getSumStats(ContasPagar::whereBetween('data_vencimento', [$dataInicio, $dataFim]));
        
        $solicitacoesStats = $getStats(Solicitacao::whereBetween('data_solicitacao', [$dataInicio, $dataFim]));

        $periodoGrafico = CarbonPeriod::create($dataInicio, $dataFim);
        $labelsGrafico = [];
        $dateMap = [];
        $i = 0;
        
        foreach ($periodoGrafico as $date) {
            $labelsGrafico[] = $date->format('d/m');
            $dateMap[$date->format('Y-m-d')] = $i++;
        }
        
        $totalDias = count($labelsGrafico);

        $getDailyStatusData = function($modelClass) use ($dataInicio, $dataFim, $dateMap, $totalDias) {
            $campoDataPagamento = ($modelClass === ContasPagar::class) ? 'data_pagamento' : 'data_recebimento';
            $statusPagos = ['Pago', 'Concluída', 'Finalizado'];

            $contas = $modelClass::where(function($query) use ($dataInicio, $dataFim, $campoDataPagamento, $statusPagos) {
                $query->where(function($q) use ($dataInicio, $dataFim, $campoDataPagamento, $statusPagos) {
                    $q->whereIn('status', $statusPagos)
                      ->whereBetween($campoDataPagamento, [$dataInicio, $dataFim]);
                })
                ->orWhere(function($q) use ($dataInicio, $dataFim, $statusPagos) {
                    $q->whereNotIn('status', $statusPagos)
                      ->whereBetween('data_vencimento', [$dataInicio, $dataFim]);
                });
            })->get();

            $pago = array_fill(0, $totalDias, 0);
            $pendente = array_fill(0, $totalDias, 0);
            $atrasado = array_fill(0, $totalDias, 0);

            foreach ($contas as $conta) {
                $status = ucfirst(strtolower(trim($conta->status))); 
                $isPago = in_array($status, $statusPagos);

                if ($isPago) {
                    $dataReferencia = $conta->$campoDataPagamento;
                } else {
                    $dataReferencia = $conta->data_vencimento;
                }

                if ($dataReferencia && isset($dateMap[$dataReferencia->format('Y-m-d')])) {
                    $idx = $dateMap[$dataReferencia->format('Y-m-d')];
                    
                    if ($isPago) {
                        $pago[$idx] += $conta->valor;
                    } 
                    elseif ($status === 'Atrasado' || $status === 'Vencido') {
                        $atrasado[$idx] += $conta->valor;
                    } 
                    else {
                        $pendente[$idx] += $conta->valor;
                    }
                }
            }

            return compact('pago', 'pendente', 'atrasado');
        };

        $dadosReceita = $getDailyStatusData(ContasReceber::class);
        $dadosDespesa = $getDailyStatusData(ContasPagar::class);

        $atividades = Activity::latest()->take(10)->get();

        return view('index', compact(
            'atividades', 'processosStats', 'orcamentosStats', 'prevStats', 
            'corrStats', 'receberStats', 'pagarStats', 'solicitacoesStats', 'contratosStats',
            'labelsGrafico', 'dadosReceita', 'dadosDespesa',
            'inputInicio', 'inputFim'
        ));
    }
}