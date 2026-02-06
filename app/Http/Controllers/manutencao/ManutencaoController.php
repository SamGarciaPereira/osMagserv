<?php

namespace App\Http\Controllers\manutencao;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Manutencao;
use App\Models\Orcamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class ManutencaoController extends Controller
{
    public function index()
    {
        $manutencoes = Manutencao::with([
            'cliente.contratos', 
            'cliente.matriz.contratos', 
            'anexos'
        ])->latest()->get();
        
        return view('manutencao.index', compact('manutencoes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => ['required', Rule::in(['Preventiva', 'Corretiva'])],
            'chamado' => 'nullable|string|max:255',
            'solicitante' => 'nullable|string|max:255',
            'descricao' => 'required|string',
            'data_inicio_atendimento' => 'nullable|date',
            'data_fim_atendimento' => 'nullable|date|after_or_equal:data_inicio_atendimento',
            'status' => ['required', Rule::in(['Pendente', 'Agendada', 'Em Andamento', 'Concluída', 'Cancelada'])],
        ]);

        Manutencao::create($validatedData);

        if ($validatedData['tipo'] === 'Corretiva') {
             return redirect()->route('manutencoes.corretiva.index')
                 ->with('success', 'Manutenção corretiva criada com sucesso!');
        } else {
             return redirect()->route('manutencoes.preventiva.index')
                 ->with('success', 'Manutenção preventiva criada com sucesso!');
        }
    }

    public function show(Manutencao $manutencao)
    {
        abort(404);
    }

    public function update(Request $request, Manutencao $manutencao)
    {
        $validatedData = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => ['required', Rule::in(['Preventiva', 'Corretiva'])], 
            'chamado' => 'nullable|string|max:255',
            'solicitante' => 'nullable|string|max:255', 
            'descricao' => 'required|string',
            'data_inicio_atendimento' => 'nullable|date',
            'data_fim_atendimento' => 'nullable|date|after_or_equal:data_inicio_atendimento',
            'status' => ['required', Rule::in(['Pendente', 'Agendada', 'Em Andamento', 'Concluída', 'Cancelada'])],
        ]);

        $manutencao->update($validatedData);

        if ($validatedData['tipo'] === 'Corretiva') {
             return redirect()->route('manutencoes.corretiva.index')
                 ->with('success', 'Manutenção corretiva atualizada com sucesso!');
        } else {
             return redirect()->route('manutencoes.preventiva.index')
                 ->with('success', 'Manutenção preventiva atualizada com sucesso!');
        }
    }

    public function destroy(Manutencao $manutencao)
    {
        $tipo = $manutencao->tipo;
        $manutencao->delete();

        if ($tipo === 'Corretiva') {
            return redirect()->route('manutencoes.corretiva.index')->with('success', 'Manutenção corretiva removida com sucesso!');
        } else {
             return redirect()->route('manutencoes.preventiva.index')->with('success', 'Manutenção preventiva removida com sucesso!');
        }
    }

    private function filtrarManutencoes(Request $request, $tipo)
    {
        $query = Manutencao::query(); 

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('solicitante', 'like', "%{$search}%")
                  ->orWhere('chamado', 'like', "%{$search}%")
                  ->orWhereHas('cliente', function($q2) use ($search) {
                      $q2->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        
        switch ($request->input('ordem')) {
            case 'antigos':
                $query->oldest(); 
                break;
            case 'data_inicio_desc': 
                $query->orderBy('data_inicio_atendimento', 'desc');
                break;
            case 'data_inicio_asc': 
                $query->orderBy('data_inicio_atendimento', 'asc'); 
                break;
            default: 
                $query->latest(); 
                break;
        }

        $inputInicio = $request->input('data_inicio_filtro');
        $inputFim    = $request->input('data_fim_filtro');

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

                $query->whereBetween('data_inicio_atendimento', [$dataInicio, $dataFim]);

            } catch (\Exception $e) {
                
            }
        }

        return $query->where('tipo', $tipo)
                     ->with([
                        'cliente.contratos',       
                        'cliente.matriz.contratos', 
                        'anexos'
                     ]) 
                     ->paginate(100);
    }

    public function createOrcamento(Manutencao $manutencao)
    {
        if($manutencao->tipo !== 'Corretiva' || !$manutencao->chamado) {
            return redirect()->back()->with('error', 'Só é possível gerar orçamento a partir de manutenções corretivas com chamado.');
        }

        $orcamento = Orcamento::create([
            'cliente_id' => $manutencao->cliente_id,
            'escopo' => "Orçamento referente a manutenção do chamado: {$manutencao->chamado}\n\n{$manutencao->descricao}",
            'status' => 'Pendente',
        ]);

        return redirect()->route('orcamentos.edit', $orcamento)
            ->with('success', 'Orçamento gerado a partir da manutenção. Por favor, preencha o restante das informações.');
    }

    public function indexCorretiva(Request $request)
    {
        $manutencoes = $this->filtrarManutencoes($request, 'Corretiva');
        return view('manutencao.manutencao-corretiva.index', compact('manutencoes'));
    }

    public function createCorretiva()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('manutencao.manutencao-corretiva.create', compact('clientes'));
    }

    public function editCorretiva(Manutencao $manutencao)
    {
        $manutencao->load('anexos');
        if ($manutencao->tipo !== 'Corretiva') {
            abort(404);
        }
        $clientes = Cliente::orderBy('nome')->get();
        return view('manutencao.manutencao-corretiva.edit', compact('manutencao', 'clientes'));
    }

    public function indexPreventiva(Request $request)
    {
        $manutencoes = $this->filtrarManutencoes($request, 'Preventiva');
        return view('manutencao.manutencao-preventiva.index', compact('manutencoes'));
    }

    public function createPreventiva()
    {
        $clientes = Cliente::orderBy('nome')->get();
        return view('manutencao.manutencao-preventiva.create', compact('clientes'));
    }

    public function editPreventiva(Manutencao $manutencao)
    {
        $manutencao->load('anexos');
        if ($manutencao->tipo !== 'Preventiva') {
             abort(404);
        }
        $clientes = Cliente::orderBy('nome')->get();
        return view('manutencao.manutencao-preventiva.edit', compact('manutencao', 'clientes'));
    }
}