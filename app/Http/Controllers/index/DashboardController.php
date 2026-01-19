<?php

namespace App\Http\Controllers\index;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        $mes = (int) $request->input('mes', now()->month);
        $ano = (int) $request->input('ano', now()->year);

        $dataInicioFiltro = Carbon::create($ano, $mes, 1)->startOfMonth();
        $dataFimFiltro    = Carbon::create($ano, $mes, 1)->endOfMonth();
        
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

        $processosStats = $getStats(Processo::whereMonth('created_at', $mes)->whereYear('created_at', $ano));

        $orcamentosStats = $getStats(Orcamento::where(function($query) use ($mes, $ano) {
            $query->where(function($q) use ($mes, $ano) {
                $q->where('status', 'Pendente')
                  ->where(function($sq) use ($mes, $ano) {
                      $sq->whereMonth('data_solicitacao', $mes)
                         ->whereYear('data_solicitacao', $ano)
                         ->orWhere(function($fq) use ($mes, $ano) {
                             $fq->whereNull('data_solicitacao')
                                ->whereMonth('created_at', $mes)
                                ->whereYear('created_at', $ano);
                         });
                  });
            })
            ->orWhere(function($q) use ($mes, $ano) {
                $q->where('status', 'Em Andamento')
                  ->whereMonth('updated_at', $mes)
                  ->whereYear('updated_at', $ano);
            })
            ->orWhere(function($q) use ($mes, $ano) {
                $q->where('status', 'Em Validação')
                  ->whereMonth('updated_at', $mes)
                  ->whereYear('updated_at', $ano);
            })
            ->orWhere(function($q) use ($mes, $ano) {
                $q->where('status', 'Validado')
                  ->whereMonth('updated_at', $mes)
                  ->whereYear('updated_at', $ano);
            })
            ->orWhere(function($q) use ($mes, $ano) {
                $q->where('status', 'Enviado')
                  ->whereMonth('data_envio', $mes)
                  ->whereYear('data_envio', $ano);
            })
            ->orWhere(function($q) use ($mes, $ano) {
                $q->where('status', 'Aprovado')
                  ->whereMonth('data_aprovacao', $mes)
                  ->whereYear('data_aprovacao', $ano);
            });
        }));
        
        $prevStats = $getStats(Manutencao::where('tipo', 'Preventiva')
            ->where(function($query) use ($mes, $ano) {
                $query->where(function($q) use ($mes, $ano) {
                    $q->whereNotNull('data_inicio_atendimento')
                    ->whereMonth('data_inicio_atendimento', $mes)
                    ->whereYear('data_inicio_atendimento', $ano);
                })->orWhere(function($q) use ($mes, $ano) {
                    $q->whereNull('data_inicio_atendimento')
                    ->whereMonth('created_at', $mes)
                    ->whereYear('created_at', $ano);
                });
            }));

        $corrStats = $getStats(Manutencao::where('tipo', 'Corretiva')
            ->where(function($query) use ($mes, $ano) {
                $query->where(function($q) use ($mes, $ano) {
                    $q->whereNotNull('data_inicio_atendimento')
                    ->whereMonth('data_inicio_atendimento', $mes)
                    ->whereYear('data_inicio_atendimento', $ano);
                })->orWhere(function($q) use ($mes, $ano) {
                    $q->whereNull('data_inicio_atendimento')
                    ->whereMonth('created_at', $mes)
                    ->whereYear('created_at', $ano);
                });
            }));

        $totalContratos = Contrato::count();

        $ativosCount = Contrato::where('ativo', true)
            ->whereDate('data_inicio', '<=', $dataFimFiltro)
            ->whereDate('data_fim', '>=', $dataInicioFiltro)
            ->count();

        $inativosCount = $totalContratos - $ativosCount;
        if ($inativosCount < 0) $inativosCount = 0;

        $contratosStats = array_filter([
            'Ativo'   => $ativosCount,
            'Inativo' => $inativosCount
        ], fn($valor) => $valor > 0);

        $receberStats = $getSumStats(ContasReceber::whereMonth('data_vencimento', $mes)->whereYear('data_vencimento', $ano));
        $pagarStats = $getSumStats(ContasPagar::whereMonth('data_vencimento', $mes)->whereYear('data_vencimento', $ano));
        
        $solicitacoesStats = $getStats(Solicitacao::whereMonth('data_solicitacao', $mes)->whereYear('data_solicitacao', $ano));

        $diasNoMes = Carbon::createFromDate($ano, $mes, 1)->daysInMonth;
        $labelsGrafico = [];
        
        for ($i = 1; $i <= $diasNoMes; $i++) {
            $labelsGrafico[] = str_pad($i, 2, '0', STR_PAD_LEFT) . '/' . $mes;
        }

        $getDailyStatusData = function($modelClass) use ($mes, $ano, $diasNoMes) {
            $campoDataPagamento = ($modelClass === ContasPagar::class) ? 'data_pagamento' : 'data_recebimento';
            
            $statusPagos = ['Pago', 'Concluída', 'Finalizado'];

            $contas = $modelClass::where(function($query) use ($mes, $ano, $campoDataPagamento, $statusPagos) {
                $query->where(function($q) use ($mes, $ano, $campoDataPagamento, $statusPagos) {
                    $q->whereIn('status', $statusPagos)
                      ->whereMonth($campoDataPagamento, $mes)
                      ->whereYear($campoDataPagamento, $ano);
                })
                ->orWhere(function($q) use ($mes, $ano, $statusPagos) {
                    $q->whereNotIn('status', $statusPagos)
                      ->whereMonth('data_vencimento', $mes)
                      ->whereYear('data_vencimento', $ano);
                });
            })->get();

            $pago = array_fill(0, $diasNoMes, 0);
            $pendente = array_fill(0, $diasNoMes, 0);
            $atrasado = array_fill(0, $diasNoMes, 0);

            foreach ($contas as $conta) {
                $status = ucfirst(strtolower(trim($conta->status))); 
                $isPago = in_array($status, $statusPagos);

                if ($isPago) {
                    $dataReferencia = $conta->$campoDataPagamento;
                } else {
                    $dataReferencia = $conta->data_vencimento;
                }

                if ($dataReferencia && $dataReferencia->month == $mes && $dataReferencia->year == $ano) {
                    $diaIndex = (int)$dataReferencia->format('d') - 1;
                    
                    if ($isPago) {
                        $pago[$diaIndex] += $conta->valor;
                    } 
                    elseif ($status === 'Atrasado' || $status === 'Vencido') {
                        $atrasado[$diaIndex] += $conta->valor;
                    } 
                    else {
                        $pendente[$diaIndex] += $conta->valor;
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
            'mes', 'ano'
        ));
    }
}