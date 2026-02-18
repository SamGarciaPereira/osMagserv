<?php

namespace App\Http\Controllers\cliente;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::with(['matriz','anexos', 'contratos', 'editor','filiais', 'history.user']);
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('responsavel', 'like', "%{$search}%")
                  ->orWhere('documento', 'like', "%{$search}%")
                  ->orWhere('telefone', 'like', "%{$search}%")
                  ->orWhere('cidade', 'like', "%{$search}%")
                  ->orWhere('estado', 'like', "%{$search}%");
            });
        }

        switch ($request->input('ordem')) {
            case 'antigos':
                $query->oldest();
                break;
            default: 
                $query->latest();
                break;
        }

        $clientes = $query->orderBy('nome')->paginate(1000);
        return view('cliente.index', compact('clientes'));

        
        
    }

    public function create()
    {
        $clientes = Cliente::whereNull('matriz_id')
                           ->orderBy('nome')
                           ->get();
        return view('cliente.create', compact ('clientes'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'matriz_id' => 'nullable|exists:clientes,id',
            'nome' => 'required|string|max:255',
            'documento' => 'required|string|max:20|unique:clientes,documento',
            'responsavel' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:clientes,email',
            'telefone' => 'nullable|string|max:20|unique:clientes,telefone',
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:50',
        ], [
            'documento.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'email.unique' => 'Este e-mail já está em uso.',
            'telefone.unique' => 'Este telefone já está vinculado a outro cliente.',
        ]);

        if (isset($validatedData['documento'])) {
        $validatedData['documento'] = preg_replace('/[^0-9]/', '', $validatedData['documento']);
    }

        Cliente::create($validatedData);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente cadastrado com sucesso!');
    }

    /**
     * Mostra o formulário para editar um cliente.
     */
    public function edit(Cliente $cliente)
    {
        $clientes = Cliente::whereNull('matriz_id')
                           ->where('id', '!=', $cliente->id)
                           ->orderBy('nome')
                           ->get();
                           
        return view('cliente.edit', compact('cliente', 'clientes'));
    }

    /**
     * Atualiza o cliente no banco de dados.
     */
    public function update(Request $request, Cliente $cliente)
    {
        $validatedData = $request->validate([
            'matriz_id' => 'nullable|exists:clientes,id',
            'nome' => 'required|string|max:255',
            'documento' => [
                'required',
                'string',
                'max:20',
                Rule::unique('clientes', 'documento')->ignore($cliente->id),
            ],
            'responsavel' => 'nullable|string|max:255',
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('clientes', 'email')->ignore($cliente->id),
            ],
            'telefone' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('clientes', 'telefone')->ignore($cliente->id),
            ],
            'cep' => 'nullable|string|max:10',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:100',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:50',
        ], [
            'documento.unique' => 'Este CPF/CNPJ já está cadastrado.',
            'email.unique' => 'Este e-mail já está em uso.',
            'telefone.unique' => 'Este telefone já está vinculado a outro cliente.',
        ]);

        if (isset($validatedData['matriz_id']) && $validatedData['matriz_id'] == $cliente->id) {
            return back()->withErrors(['matriz_id' => 'Um cliente não pode ser matriz de si mesmo.']);
        }

        if (isset($validatedData['documento'])) {
        $validatedData['documento'] = preg_replace('/[^0-9]/', '', $validatedData['documento']);
    }

        $cliente->update($validatedData);

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
                         ->with('success', 'Cliente removido com sucesso!');
    }
}
