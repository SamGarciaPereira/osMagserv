<?php

namespace App\Http\Controllers\processo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Processo;
use App\Models\ContasReceber;
use App\Models\Orcamento;

class ProcessoController extends Controller
{
    public function index(Request $request)
    {
       $query = Processo::with('orcamento.cliente', 'orcamento.anexos', 'anexos', 'contasReceber', 'editor', 'history.user')
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

    public function create()
    {
    }

    public function store(Request $request)
    {
    }

    public function show(string $id)
    {
    }

    public function edit(Processo $processo)
    {
        $processo->load(['orcamento', 'contasReceber']);
        return view('processo.edit', compact('processo'));
    }

    public function update(Request $request, Processo $processo)
    {
        $validatedData = $request->validate([
            'status' => 'required|in:Em Aberto,Finalizado,Faturado',
            'nf' => 'nullable|string|max:255',
            'parcelas' => 'nullable|array',
            'parcelas.*.id' => 'nullable|integer|exists:contas_recebers,id',
            'parcelas.*.descricao' => 'nullable|string',
            'parcelas.*.valor' => 'required|numeric|min:0',
            'parcelas.*.data_vencimento' => 'nullable|date',
            'parcelas.*.nf' => [
                'nullable',
                'string',
                'distinct', 
                function ($attribute, $value, $fail) use ($request) {
                    if (empty($value)) {
                        return;
                    }

                    $index = explode('.', $attribute)[1];
                    $currentId = $request->input("parcelas.{$index}.id");

                    $query = ContasReceber::where('nf', $value);

                    if ($currentId) {
                        $query->where('id', '!=', $currentId);
                    }

                    if ($query->exists()) {
                        $fail("A NF {$value} já está cadastrada em outro processo.");
                    }
                },
            ],
        ]);

        $totalParcelas = 0;
        if ($request->has('parcelas')) {
            foreach ($request->parcelas as $dadosParcela) {
                $totalParcelas += $dadosParcela['valor'];
            }
        }

        $valorOrcamento = $processo->orcamento->valor;

        if ($totalParcelas > ($valorOrcamento + 0.01)) {
            return back()->withInput()->withErrors([
                'status' => "O valor total das parcelas excede o valor do orçamento."
            ]);
        }

        $nfsColetadas = [];
        if ($request->has('parcelas')) {
            foreach ($request->parcelas as $parcela) {
                if (!empty($parcela['nf'])) {
                    $nfsColetadas[] = trim($parcela['nf']);
                }
            }
        }

        if (count($nfsColetadas) > 0) {
            $validatedData['nf'] = implode(', ', array_unique($nfsColetadas));
        }

        $parcelasIdsMantidos = [];

        if ($request->has('parcelas')) {
            foreach ($request->parcelas as $dadosParcela) {
                $data = [
                    'processo_id' => $processo->id,
                    'cliente_id' => $processo->orcamento->cliente_id,
                    'descricao' => !empty($dadosParcela['descricao']) ? $dadosParcela['descricao'] : "",
                    'valor' => $dadosParcela['valor'],
                    'data_vencimento' => $dadosParcela['data_vencimento'] ?? null,
                    'nf' => $dadosParcela['nf'] ?? null,
                    'status' => isset($dadosParcela['id']) 
                                ? ContasReceber::find($dadosParcela['id'])->status 
                                : 'Pendente',
                    'last_user_id' => auth()->id(),
                ];

                if (isset($dadosParcela['id'])) {
                    $conta = $processo->contasReceber()->find($dadosParcela['id']);
                    if ($conta) {
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

        $processo->update([
            'status' => $validatedData['status'],
            'nf' => $validatedData['nf'] ?? null,
            'last_user_id' => auth()->id()
        ]);

        return redirect()->route('processos.index')
            ->with('success', 'Processo atualizado com sucesso!');
    }

    public function destroy(string $id)
    {
    }
}