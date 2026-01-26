<?php

namespace App\Http\Controllers\orcamento;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\CodeGeneratorService;
use Illuminate\Support\Str;

class OrcamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Orcamento::with('cliente');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('numero_proposta', 'like', "%{$search}%")
                    ->orWhere('escopo', 'like', "%{$search}%")
                    ->orWhereHas('cliente', function ($q2) use ($search) {
                        $q2->where('nome', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        switch ($request->input('ordem')) {
            case 'recentes':
                $query->orderBy('data_solicitacao', 'desc');
                break;
            case 'antigos':
                $query->orderBy('data_solicitacao', 'asc');
                break;
            case 'maior_valor':
                $query->orderByDesc('valor');
                break;
            case 'menor_valor':
                $query->orderBy('valor');
                break;
            case 'envio':
                $query->orderByDesc('data_envio');
                break;
            case 'aprovacao':
                $query->orderByDesc('data_aprovacao');
                break;
            default:
                $query->orderBy('data_solicitacao', 'desc');
                break;
        }

        if ($request->filled('mes_ano')) {
            $data = \Carbon\Carbon::createFromFormat('Y-m', $request->mes_ano);
            $query->whereMonth('data_solicitacao', $data->month)
                  ->whereYear('data_solicitacao', $data->year);
        }

        $orcamentos = $query->paginate(200);
        return view('orcamento.index', compact('orcamentos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('orcamento.create', compact('clientes'));
    }

    public function store(Request $request)
    {

        $request->merge([
            'data_solicitacao' => $request->data_solicitacao ?: null,
            'data_envio'       => $request->data_envio ?: null,
            'data_aprovacao'   => $request->data_aprovacao ?: null,
        ]);

        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'numero_manual' => 'nullable|integer|min:1',
            'valor' => 'required_if:status,Em Validação,Validado,Enviado,Aprovado|nullable|numeric|min:0',
            'status' => 'required|string|in:Pendente,Em Andamento,Em Validação,Validado,Enviado,Aprovado',
            'revisao' => 'nullable|integer|min:0',
            'escopo' => 'nullable|string',
            'comentario' => 'nullable|string',
            'checklist_data' => 'nullable|json',
            'data_solicitacao' => 'required_if:status,Pendente,Em Andamento|nullable|date',
            'data_envio' => 'required_if:status,Enviado|nullable|date',
            'data_aprovacao' => 'required_if:status,Aprovado|nullable|date',
        ],[
            'valor.required_if' => 'Preencha o valor para o status selecionado!',
            'data_solicitacao.required_if' => 'A Data de Solicitação é obrigatória para status Pendente ou Em Andamento.',
            'data_envio.required_if'       => 'A Data de Envio é obrigatória para status Enviado.',
            'data_aprovacao.required_if'   => 'A Data de Aprovação é obrigatória para status Aprovado.',
        ]);

        if ($request->filled('numero_manual')) {
            $generator = new CodeGeneratorService();
            $cliente = Cliente::find($request->cliente_id);

            $codigoProvavel = $generator->formatarCodigoOrcamento($cliente, now(), $request->numero_manual);

            if (Orcamento::where('numero_proposta', $codigoProvavel)->exists()) {
                return back()
                    ->withInput()
                    ->withErrors(['numero_manual' => "A proposta {$codigoProvavel} já existe."]);
            }
        }

        $orcamento = new Orcamento($validatedData);
        $orcamento->numero_manual = $request->numero_manual;
        $orcamento->save();

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento cadastrado com sucesso!');
    }



    public function edit(Orcamento $orcamento)
    {
        $orcamento->load('anexos');
        $clientes = Cliente::orderBy('nome')->get();
        return view('orcamento.edit', compact('orcamento', 'clientes'));
    }

    public function update(Request $request, Orcamento $orcamento)
    {

        $request->merge([
            'data_solicitacao' => $request->data_solicitacao ?: null,
            'data_envio'       => $request->data_envio ?: null,
            'data_aprovacao'   => $request->data_aprovacao ?: null,
        ]);

        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'numero_proposta_sufixo' => ['required', 'integer', 'min:0', 'max:999'],
            'valor' => 'required_if:status,Em Validação,Validado,Enviado,Aprovado|nullable|numeric|min:0',
            'status' => 'required|string|in:Pendente,Em Andamento,Em Validação,Validado,Enviado,Aprovado',
            'revisao' => 'nullable|integer|min:0',
            'escopo' => 'nullable|string',
            'comentario' => 'nullable|string',
            'checklist_data' => 'nullable|json',
            'data_solicitacao' => 'required_if:status,Pendente,Em Andamento|nullable|date',
            'data_envio' => 'required_if:status,Enviado|nullable|date',
            'data_aprovacao' => 'required_if:status,Aprovado|nullable|date',
        ],[
            'valor.required_if' => 'Preencha o valor para o status selecionado!',
            'data_solicitacao.required_if' => 'A Data de Solicitação é obrigatória para status Pendente ou Em Andamento.',
            'data_envio.required_if'       => 'A Data de Envio é obrigatória para status Enviado.',
            'data_aprovacao.required_if'   => 'A Data de Aprovação é obrigatória para status Aprovado.',
        ]);

        $prefixo = Str::beforeLast($orcamento->numero_proposta, '-');
        $sufixo = (int) $validatedData['numero_proposta_sufixo'];
        $novoNumero = $prefixo . '-' . str_pad($sufixo, 3, '0', STR_PAD_LEFT);

        if (Orcamento::where('numero_proposta', $novoNumero)->where('id', '!=', $orcamento->id)->exists()) {
            return back()->withInput()->withErrors(['numero_proposta_sufixo' => 'Esta proposta já existe.']);
        }

        $validatedData['numero_proposta'] = $novoNumero;
        $validatedData['data_envio'] = $validatedData['data_envio'] ?? null;
        $validatedData['data_aprovacao'] = $validatedData['data_aprovacao'] ?? null;
        $validatedData['data_solicitacao'] = $validatedData['data_solicitacao'] ?? null;

        if ($request->filled('checklist_data')) {
            $validatedData['checklist'] = json_decode($request->checklist_data, true);
        } else {
            $validatedData['checklist'] = [];
        }

        unset($validatedData['checklist_data']);

        $temPendencias = collect($validatedData['checklist'])->contains('completed', false);

        $statusProibidos = ['Em Validação', 'Validado', 'Enviado', 'Aprovado'];

        if ($temPendencias && in_array($validatedData['status'], $statusProibidos)) {
            return back()
                ->withInput() 
                ->withErrors([
                    'status' => 'Existem tarefas pendentes no checklist! Conclua todas as tarefas antes de alterar para os status seguintes!'
                ]);
        }

        $orcamento->update($validatedData);

        return redirect()->route('orcamentos.index')
            ->with('success', 'Orçamento atualizado com sucesso!');
    }

    public function destroy(Orcamento $orcamento)
    {
        $orcamento->delete();
        return redirect()->route('orcamentos.index')->with('success', 'Orçamento removido com sucesso!');
    }
}
