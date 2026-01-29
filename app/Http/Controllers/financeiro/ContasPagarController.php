<?php

namespace App\Http\Controllers\financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContasPagar;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ContasPagarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
      $query = ContasPagar::query();

      if ($request->filled('search')) {
        $search = $request->input('search');
        $query->where(function($q) use ($search) {
            $q->where('descricao', 'like', "%{$search}%")
                ->orWhere('danfe', 'like', "%{$search}%")
                ->orWhere('fornecedor', 'like', "%{$search}%");

        });
      }

      if($request->filled('status')){
        $query->where('status', $request->input('status'));
      }

      switch ($request->input('ordem')) {
            case 'vencimento_asc':
                $query->orderBy('data_vencimento', 'asc');
                break;
            case 'vencimento_desc':
                $query->orderBy('data_vencimento', 'desc');
                break;
            case 'maior_valor':
                $query->orderByDesc('valor');
                break;
            case 'recentes':
                $query->latest();
                break;
            default:
                $query->orderBy('data_vencimento', 'asc');
                break;
        }

        if ($request->filled('mes_ano')) {
            $data = Carbon::createFromFormat('Y-m', $request->mes_ano);
            $query->whereMonth('data_vencimento', $data->month)
                  ->whereYear('data_vencimento', $data->year);
        }

        $contasFixas = $query->clone()->where('fixa', true)->paginate(1000, ['*'], 'page_fixas');
        $contasVariaveis = $query->clone()->where('fixa', false)->paginate(1000, ['*'], 'page_variaveis');

        return view('financeiro.contas-pagar.index', compact('contasFixas', 'contasVariaveis'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('financeiro.contas-pagar.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'fornecedor' => 'required|string|max:255',
            'descricao'  => 'required|string|max:255',
            'danfe'      => 'nullable|string|max:100',
            'valor'      => 'required|numeric|min:0.01',
            'status'     => 'required|in:Pendente,Pago,Atrasado',
            'data_pagamento' => 'required_if:status,Pago|nullable|date',
            'data_vencimento' => 'required_if:tipo_recorrencia,unica,parcelada|nullable|date',
            'tipo_recorrencia' => 'required|in:unica,parcelada,fixa',
            'qtd_parcelas'     => 'required_if:tipo_recorrencia,parcelada|nullable|integer|min:2',
            'dia_fixo'         => 'required_if:tipo_recorrencia,fixa|nullable|integer|min:1|max:31',
        ], [
            'data_pagamento.required_if' => 'A Data de Pagamento é obrigatória quando o status é Pago',
            'data_vencimento.required_if' => 'A Data de Vencimento é obrigatória.',
        ]);

        if (isset($validatedData['data_pagamento']) && $validatedData['data_pagamento'] !== null) {
            $validatedData['status'] = 'Pago';
        }

        $isFixa = ($request->tipo_recorrencia === 'fixa');
        
        $tipo = $request->tipo_recorrencia;
        
        // CASO 1: ÚNICA
        if ($tipo === 'unica') {
            $data = $validatedData;
            $data['fixa'] = false; 
            ContasPagar::create($data);
        }

        // CASO 2: PARCELADA
        elseif ($tipo === 'parcelada') {
            $valorParcela = $validatedData['valor']; 
            
            $dataBase = Carbon::parse($validatedData['data_vencimento']);

            for ($i = 0; $i < $request->qtd_parcelas; $i++) {
                $novaConta = $validatedData;
                
                $novaConta['descricao'] = $validatedData['descricao'] . " " . ($i + 1) . "/" . $request->qtd_parcelas;
                $novaConta['valor'] = $valorParcela; 
                $novaConta['data_vencimento'] = $dataBase->copy()->addMonths($i);
                $novaConta['fixa'] = false;

                if ($i > 0) {
                    $novaConta['status'] = 'Pendente';
                    $novaConta['data_pagamento'] = null;
                }

                ContasPagar::create($novaConta);
            }
        }

        // CASO 3: FIXA (Mensal até o fim do ano)
        elseif ($tipo === 'fixa') {
            $diaFixo = (int) $request->dia_fixo;
            $mesAtual = now()->month;
            $anoAtual = now()->year;

            for ($mes = $mesAtual; $mes <= 12; $mes++) {
                $novaConta = $validatedData;

                try {
                    $dtVencimento = Carbon::create($anoAtual, $mes, $diaFixo);
                } catch (\Exception $e) {
                    $dtVencimento = Carbon::create($anoAtual, $mes, 1)->endOfMonth();
                }

                $novaConta['data_vencimento'] = $dtVencimento;
                $novaConta['fixa'] = true;

                if ($dtVencimento->month > $mesAtual) {
                    $novaConta['status'] = 'Pendente';
                    $novaConta['data_pagamento'] = null;
                }

                ContasPagar::create($novaConta);
            }
        }

        return redirect()->route('financeiro.contas-pagar.index')
                        ->with('success', 'Conta(s) a pagar cadastrada(s) com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContasPagar $contasPagar)
    {
        $contasPagar->load('anexos');
        return view('financeiro.contas-pagar.edit', compact('contasPagar'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContasPagar $contasPagar)
    {
        $regras = [
            'fornecedor'      => 'required|string|max:255',
            'descricao'       => 'required|string|max:255',
            'danfe'           => 'nullable|string|max:100',
            'valor'           => 'required|numeric|min:0.01',
            'status'          => 'required|in:Pendente,Pago,Atrasado',
            'data_pagamento'  => 'required_if:status,Pago|nullable|date',
        ];

        if ($contasPagar->is_fixa) {
            $regras['dia_fixo'] = 'required|integer|min:1|max:31';
        } else {
            $regras['data_vencimento'] = 'required|date';
        }

        $validatedData = $request->validate($regras, [
            'data_pagamento.required_if' => 'A Data de Pagamento é obrigatória quando o status é Pago.',
            'dia_fixo.required' => 'O Dia do Vencimento é obrigatório para contas fixas.',
        ]);

        if (!empty($validatedData['data_pagamento'])) {
            $validatedData['status'] = 'Pago';
        } elseif ($validatedData['status'] !== 'Pago') {
            $validatedData['data_pagamento'] = null;
        }

        if ($contasPagar->is_fixa) {
            $dataOriginal = \Carbon\Carbon::parse($contasPagar->data_vencimento);
            $novoDia = (int) $request->dia_fixo;
            
            try {
                $novaData = $dataOriginal->copy()->day($novoDia);
            } catch (\Exception $e) {
                $novaData = $dataOriginal->copy()->endOfMonth();
            }
            
            $validatedData['data_vencimento'] = $novaData;
            unset($validatedData['dia_fixo']); 
        }

        // Atualiza
        $contasPagar->update($validatedData);

        return redirect()->route('financeiro.contas-pagar.index')
                         ->with('success', 'Conta atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContasPagar $contasPagar)
    {
        $contasPagar->delete();

        return redirect()->route('financeiro.contas-pagar.index')
                         ->with('success', 'Conta a pagar excluída com sucesso!');  
    }
}
