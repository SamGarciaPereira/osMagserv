<?php

namespace App\Http\Controllers\processo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Processo;
use App\Models\ContasReceber;
use App\Models\Orcamento;

class ProcessoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
       $query = Processo::with('orcamento.cliente', 'orcamento.anexos', 'anexos', 'contasReceber')
            ->join('orcamentos', 'processos.orcamento_id', '=', 'orcamentos.id')
            ->select('processos.*');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('processos.nf', 'like', "%{$search}%")
                  ->orWhereHas('orcamento', function($q2) use ($search) {
                      $q2->where('numero_proposta', 'like', "%{$search}%")
                         ->orWhere('escopo', 'like', "%{$search}%")
                         ->orWhere('valor', 'like', "%{$search}%")
                         ->orWhereHas('cliente', function($q3) use ($search) {
                             $q3->where('nome', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                         });
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('processos.status', $request->input('status'));
        }

        switch ($request->input('ordem')) {
            case 'antigos':
                $query->orderBy('orcamentos.data_aprovacao', 'asc');
                break;
            case 'maior_valor':
                $query->orderByDesc('orcamentos.valor');
                break;
            case 'menor_valor':
                $query->orderBy('orcamentos.valor');
                break;
            case 'recentes':
            default:
                $query->orderBy('orcamentos.data_aprovacao', 'desc');
                break;
        }

        if ($request->filled('mes_ano')) {
            try {
                $data = \Carbon\Carbon::createFromFormat('Y-m', $request->input('mes_ano'));
                
                $query->whereMonth('orcamentos.data_aprovacao', $data->month)
                      ->whereYear('orcamentos.data_aprovacao', $data->year);
            } catch (\Exception $e) {
            }
        }

        $processos = $query->paginate(1000); 
        return view('processo.index', compact('processos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function edit(Processo $processo)
    {
        $processo->load(['orcamento', 'contasReceber']);
        return view('processo.edit', compact('processo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Processo $processo)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Em Aberto,Finalizado,Faturado',
            'nf' => 'nullable|string|max:255',
            'parcelas' => 'nullable|array',
            'parcelas.*.descricao' => 'required|string',
            'parcelas.*.valor' => 'required|numeric|min:0',
            'parcelas.*.data_vencimento' => 'nullable|date',
            'parcelas.*.nf' => 'nullable|string',
        ]);

        $parcelasIdsMantidos = [];
        $totalParcelas = 0;

        if ($request->has('parcelas')) {
            foreach ($request->parcelas as $dadosParcela) {
                $totalParcelas += $dadosParcela['valor'];
            }
        }

        $valorOrcamento = $processo->orcamento->valor;
        if ($totalParcelas > ($valorOrcamento + 0.01)) {
            return back()->withInput()->withErrors([
                'status' => "O valor total das parcelas (R$ " . number_format($totalParcelas, 2, ',', '.') . ") excede o valor do orçamento (R$ " . number_format($valorOrcamento, 2, ',', '.') . ")."
            ]);
        }

        if ($request->has('parcelas')) {
            foreach ($request->parcelas as $dadosParcela) {
                $data = [
                    'processo_id' => $processo->id,
                    'cliente_id' => $processo->orcamento->cliente_id,
                    'descricao' => $dadosParcela['descricao'],
                    'valor' => $dadosParcela['valor'],
                    'data_vencimento' => $dadosParcela['data_vencimento'],
                    'nf' => $dadosParcela['nf'] ?? null,
                    'status' => isset($dadosParcela['id']) 
                                ? ContasReceber::find($dadosParcela['id'])->status 
                                : 'Pendente',
                ];

                $totalParcelas += $data['valor'];

                if (isset($dadosParcela['id'])) {
                    $conta = ContasReceber::find($dadosParcela['id']);
                    if ($conta && $conta->processo_id == $processo->id) {
                        $conta->update($data);
                        $parcelasIdsMantidos[] = $conta->id;
                    }
                } else {
                    $novaConta = ContasReceber::create($data);
                    $parcelasIdsMantidos[] = $novaConta->id;
                }
            }
        }

        $processo->contasReceber()->whereNotIn('id', $parcelasIdsMantidos)->delete();

        if ($validatedData['status'] === 'Faturado') {
            $valorOrcamento = $processo->orcamento->valor;
            
            if (empty($valorOrcamento) || $valorOrcamento <= 0) {
                 return back()->withInput()->withErrors(['status' => 'O orçamento não possui valor definido.']);
            }
        }

        $processo->update([
            'status' => $validatedData['status'],
            'nf' => $validatedData['nf']
        ]);

        return redirect()->route('processos.index')
            ->with('success', 'Processo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
