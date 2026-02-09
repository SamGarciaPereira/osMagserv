<?php

namespace App\Http\Controllers\contrato;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Services\CodeGeneratorService;

class ContratoController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrato::query();
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('clientes', function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%");
            })->orWhere('numero_contrato', 'like', "%{$search}%");    
        }
        $contratos = $query->with(['clientes.filiais', 'anexos', 'editor', 'history.user'])
                           ->latest()
                           ->paginate(1000);
        return view('contrato.index', compact('contratos'));
    }

    public function create()
    {
        $clientes = Cliente::whereNull('matriz_id')
                           ->orderBy('nome')
                           ->get();
        return view('contrato.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'clientes' => 'required|array|min:1',
            'clientes.*' => 'exists:clientes,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $validatedData['ativo'] = true;

        $matriz = Cliente::find($request->clientes[0]);
        
        $generator = new CodeGeneratorService();
        $validatedData['numero_contrato'] = $generator->gerarCodigoContrato($matriz);

        $contrato = Contrato::create($validatedData);

        $contrato->clientes()->attach($request->clientes);

        return redirect()->route('contratos.index')
                         ->with('success', "Contrato '{$contrato->numero_contrato}' criado para a Matriz (e filiais).");
    }

    public function edit(Contrato $contrato)
    {
        $contrato->load('clientes');
        $clientes = Cliente::whereNull('matriz_id')
                           ->orderBy('nome')
                           ->get();
        return view('contrato.edit', compact('contrato', 'clientes'));
    }

    public function update(Request $request, Contrato $contrato)
    {
        $validatedData = $request->validate([
            'clientes' => 'required|array|min:1',
            'clientes.*' => 'exists:clientes,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
            'ativo' => 'boolean',
        ]);

        $contrato->update([
            'data_inicio' => $validatedData['data_inicio'],
            'data_fim' => $validatedData['data_fim'],
            'ativo' => $request->has('ativo') ? $validatedData['ativo'] : $contrato->ativo,
        ]);

        $contrato->clientes()->sync($request->clientes);

        return redirect()->route('contratos.index')
            ->with('success', 'Contrato atualizado com sucesso!');
    }

    public function destroy(Contrato $contrato)
    {
        $contrato->delete();
        return redirect()->route('contratos.index')
                         ->with('success', "Contrato excluído com sucesso.");
    }
}