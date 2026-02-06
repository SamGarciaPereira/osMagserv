<?php

namespace App\Http\Controllers\financeiro;

use App\Http\Controllers\Controller;
use App\Models\ContasReceber;
use App\Models\Cliente;
use App\Models\Processo;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ContasReceberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $query = ContasReceber::with(['cliente', 'processo.orcamento', 'anexos', 'history.user', 'editor']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                 $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('nf', 'like', "%{$search}%")
                  ->orWhereHas('processo', function($q1) use ($search) {
                        $q1->where('nf', 'like', "%{$search}%");
                  })
                  ->orWhereHas('cliente', function($q2) use ($search) {
                        $q2->where('nome', 'like', "%{$search}%");
                  });
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
            default:
                $query->orderBy('data_vencimento', 'asc');
                break;
        }

        $inputInicio = $request->input('data_inicio');
        $inputFim    = $request->input('data_fim');


        if ($inputInicio) {
            try {
                $dataInicio = Carbon::parse($inputInicio)->startOfMonth();

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

                $query->whereBetween('data_vencimento', [$dataInicio, $dataFim]);

            } catch (\Exception $e) {
                
            }
        }

        $contasReceber = $query->get();

        return view('financeiro.contas-receber.index', compact('contasReceber'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('financeiro.contas-receber.create', compact('clientes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'descricao' => 'required|string|max:255',
            'nf' => 'nullable|string|max:100',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_recebimento' => 'required_if:status,Pago|nullable|date',
            'status' => 'required|in:Pendente,Pago,Atrasado',
        ],[
            'data_recebimento.required_if' => 'A Data de Recebimento é obrigatória quando o status é Pago',
        ]);

        if($validatedData['data_recebimento'] !== null){
            $validatedData['status'] = 'Pago';
        }

        ContasReceber::create($validatedData);

        return redirect()->route('financeiro.contas-receber.index')
                         ->with('success', 'Conta a receber cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContasReceber $contasReceber)
    {
        return view('financeiro.contas-receber.edit', compact('contasReceber'));
    }   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContasReceber $contasReceber)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'descricao' => 'required|string|max:255',
            'valor' => 'required|numeric|min:0.01',
            'data_vencimento' => 'required|date',
            'data_recebimento' => 'required_if:status,Pago|nullable|date',
            'status' => 'required|in:Pendente,Pago,Atrasado',
        ],[
            'data_recebimento.required_if' => 'A Data de Recebimento é obrigatória quando o status é Pago',
        ]);

        if($validatedData['data_recebimento'] !== null){
            $validatedData['status'] = 'Pago';
        }

        $contasReceber->update($validatedData);

        return redirect()->route('financeiro.contas-receber.index')
                         ->with('success', 'Conta a receber atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContasReceber $contasReceber)
    {
        $contasReceber->delete();

        return redirect()->route('financeiro.contas-receber.index')
                         ->with('success', 'Conta a receber removida com sucesso!');
    }
}