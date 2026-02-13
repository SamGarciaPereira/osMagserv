<?php

namespace App\Http\Controllers\rh;

use App\Models\Funcionario;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Collection;

class FuncionarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Funcionario::with(['anexos', 'editor', 'history.user']);

        $funcionariosFixos = $query->clone()->where('tipo_contrato', 'Fixo')->orderBy('nome')->paginate(1000, ['*'], 'page_fixos');
        $funcionariosIntermitentes = $query->clone()->where('tipo_contrato', 'Intermitente')->orderBy('nome')->paginate(1000, ['*'], 'page_intermitentes');
        $funcionariosPJ = $query->clone()->where('tipo_contrato', 'PJ')->orderBy('nome')->paginate(1000, ['*'], 'page_pj');
        $funcionariosEstagio = $query->clone()->where('tipo_contrato', 'Estagio')->orderBy('nome')->paginate(1000, ['*'], 'page_estagio');
        return view('rh.funcionario.index', compact('funcionariosFixos', 'funcionariosIntermitentes', 'funcionariosPJ', 'funcionariosEstagio'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('rh.funcionario.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:20|unique:funcionarios,cpf',
            'rg' => 'nullable|string|max:20|unique:funcionarios,rg',
            'data_nascimento' => 'nullable|date',
            'estado_nascimento' => 'nullable|string|max:255',
            'cidade_nascimento' => 'nullable|string|max:255',
            'estado_civil' => 'nullable|string|max:255',
            'sexo' => 'nullable|string|max:10',
            'numero_filhos' => 'nullable|integer|min:0',
            'foto_perfil' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:255|unique:funcionarios,email',
            'telefone' => 'nullable|string|max:20|unique:funcionarios,telefone',
            'cep' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'tipo_contrato' => ['required', Rule::in(['Fixo', 'Intermitente', 'PJ', 'Estagio'])],
            'data_admissao' => 'nullable|date',
            'data_demissao' => 'nullable|date|after_or_equal:data_admissao',
            'ativo' => 'boolean',
            'status_documentos' => 'nullable|in:Em dia,Atencao,Vencido',
            'observacoes' => 'nullable|string',
            'doc_aso' => 'nullable|date',
            'doc_ordem_servico' => 'nullable|date',
            'doc_ficha_epi' => 'nullable|date',
            'doc_nr06' => 'nullable|date',
            'doc_nr10' => 'nullable|date',
            'doc_nr12' => 'nullable|date',
            'doc_nr18' => 'nullable|date',
            'doc_nr35' => 'nullable|date',
            'doc_contrato_intermitente' => 'nullable|date',
        ],
        [
            'cpf.unique' => 'O CPF informado já está em uso por outro funcionário.',
            'rg.unique' => 'O RG informado já está em uso por outro funcionário.',
            'email.unique' => 'O email informado já está em uso por outro funcionário.',
            'telefone.unique' => 'O telefone informado já está em uso por outro funcionário.',
            'data_demissao.after_or_equal' => 'A data de demissão deve ser igual ou posterior à data de admissão.'
        ]);

        if ($request->hasFile('foto_perfil')) {
            $path = $request->file('foto_perfil')->store('fotos_funcionarios', 'public');
            $validatedData['foto_perfil'] = $path;
        }

        $validatedData['cpf'] = $request->filled('cpf') 
        ? preg_replace('/[^0-9]/', '', $request->cpf) 
        : null;

        $validatedData['rg'] = $request->filled('rg') 
            ? $request->rg 
            : null;

        $validatedData['telefone'] = $request->filled('telefone') 
            ? preg_replace('/[^0-9]/', '', $request->telefone) 
            : null;

        $validatedData['email'] = $request->filled('email') 
            ? $request->email 
            : null;

        Funcionario::create($validatedData);

        return redirect()->route('rh.funcionarios.index')->with('success', 'Funcionário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Funcionario $funcionario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Funcionario $funcionario)
    {
        $funcionario->load(['anexos', 'editor', 'history.user']);
        return view('rh.funcionario.edit', compact('funcionario'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Funcionario $funcionario)
    {
        $validatedData = $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'nullable|string|max:20|unique:funcionarios,cpf,' . $funcionario->id,
            'rg' => 'nullable|string|max:20|unique:funcionarios,rg,' . $funcionario->id,
            'data_nascimento' => 'nullable|date',
            'estado_nascimento' => 'nullable|string|max:255',
            'cidade_nascimento' => 'nullable|string|max:255',
            'estado_civil' => 'nullable|string|max:255',
            'sexo' => 'nullable|string|max:10',
            'numero_filhos' => 'nullable|integer|min:0',
            'foto_perfil' => 'nullable|image|max:2048',
            'email' => 'nullable|email|max:255|unique:funcionarios,email,' . $funcionario->id,
            'telefone' => 'nullable|string|max:20|unique:funcionarios,telefone,' . $funcionario->id,
            'cep' => 'nullable|string|max:20',
            'logradouro' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:255',
            'cargo' => 'nullable|string|max:255',
            'tipo_contrato' => ['required', Rule::in(['Fixo', 'Intermitente', 'PJ', 'Estagio'])],
            'data_admissao' => 'nullable|date',
            'data_demissao' => 'nullable|date|after_or_equal:data_admissao',
            'ativo' => 'boolean',
            'status_documentos' => 'nullable|in:Em dia,Atencao,Vencido',
            'observacoes' => 'nullable|string',
            'doc_aso' => 'nullable|date',
            'doc_ordem_servico' => 'nullable|date',
            'doc_ficha_epi' => 'nullable|date',
            'doc_nr06' => 'nullable|date',
            'doc_nr10' => 'nullable|date',
            'doc_nr12' => 'nullable|date',
            'doc_nr18' => 'nullable|date',
            'doc_nr35' => 'nullable|date',
            'doc_contrato_intermitente' => 'nullable|date',
        ],
        [
            'cpf.unique' => 'O CPF informado já está em uso por outro funcionário.',
            'rg.unique' => 'O RG informado já está em uso por outro funcionário.',
            'email.unique' => 'O email informado já está em uso por outro funcionário.',
            'telefone.unique' => 'O telefone informado já está em uso por outro funcionário.',
            'data_demissao.after_or_equal' => 'A data de demissão deve ser igual ou posterior à data de admissão.'
        ]);

        if ($request->hasFile('foto_perfil')) {
            $path = $request->file('foto_perfil')->store('fotos_funcionarios', 'public');
            $validatedData['foto_perfil'] = $path;
        }
        if(isset($validatedData['cpf'])) {
            $validatedData['cpf'] = preg_replace('/[^0-9]/', '', $validatedData['cpf']);
        }
        if(isset($validatedData['telefone'])) {
            $validatedData['telefone'] = preg_replace('/[^0-9]/', '', $validatedData['telefone']);
        }
        $funcionario->update($validatedData);

        return redirect()->route('rh.funcionarios.index')->with('success', 'Funcionário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Funcionario $funcionario)
    {
        $funcionario->delete();

        return redirect()->route('rh.funcionarios.index')->with('success', 'Funcionário removido com sucesso.');
    }
}
