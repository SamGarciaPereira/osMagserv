@extends('layouts.main')

@section('title', 'Magserv | Editar Funcionário')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Funcionário</h1>
            <p class="text-gray-600 mt-1">Altere os dados do funcionário {{ $funcionario->nome }}.</p>
        </div>
        <a href="{{ route('rh.funcionarios.index') }}"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg">
            Voltar para a Lista
        </a>
    </div>
    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('rh.funcionarios.update', $funcionario->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-2">Informações Pessoais</h2>
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" name="nome"
                        value="{{ old('nome', $funcionario->nome ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: João da Silva" required>
                </div>
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                    <input type="text" id="cpf" name="cpf"
                        value="{{ old('cpf', $funcionario->cpf ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 123.456.789-00">
                </div>
                <div>
                    <label for="rg" class="block text-sm font-medium text-gray-700 mb-2">RG</label>
                    <input type="text" id="rg" name="rg"
                        value="{{ old('rg', $funcionario->rg ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 12.345.678-9">
                </div>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="telefone" name="telefone"
                        value="{{ old('telefone', $funcionario->telefone ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: (11) 99999-8888">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email', $funcionario->email ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: exemplo@dominio.com">
                </div>
                <div>
                    <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento"
                        value="{{ old('data_nascimento', $funcionario->data_nascimento ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Dados contratuais</h2>
                <div>
                    <label for="cargo" class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                    <input type="text" id="cargo" name="cargo"
                        value="{{ old('cargo', $funcionario->cargo ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Técnico de Manutenção">
                </div>
                <div>
                    <label for="tipo_contrato" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato <span class="text-red-500">*</span></label>
                    <select name="tipo_contrato" id="tipo_contrato"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecione o tipo de contrato</option>
                        <option value="Fixo" {{ old('tipo_contrato', $funcionario->tipo_contrato ?? '') == 'Fixo' ? 'selected' : '' }}>CLT (Fixo)</option>
                        <option value="PJ" {{ old('tipo_contrato', $funcionario->tipo_contrato ?? '') == 'PJ' ? 'selected' : '' }}>PJ</option>
                        <option value="Estagio" {{ old('tipo_contrato', $funcionario->tipo_contrato ?? '') == 'Estagio' ? 'selected' : '' }}>Estágio</option>
                        <option value="Intermitente" {{ old('tipo_contrato', $funcionario->tipo_contrato ?? '') == 'Intermitente' ? 'selected' : '' }}>Intermitente</option>
                    </select>
                </div>
                <div>
                    <label for="data_admissao" class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão</label>
                    <input type="date" id="data_admissao" name="data_admissao"
                        value="{{ old('data_admissao', $funcionario->data_admissao ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="data_demissao" class="block text-sm font-medium text-gray-700 mb-2">Data de Demissão</label>
                    <input type="date" id="data_demissao" name="data_demissao"
                        value="{{ old('data_demissao', $funcionario->data_demissao ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div id="section-documentos" class="col-span-full hidden transition-all duration-300 ease-in-out">
    
                    <h2 class="text-xl font-semibold text-gray-800 mt-6 mb-4 pb-2">
                        Documentos Funcionários Fixos/Intermit. (Data de Emissão)
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        
                        <div id="field-contrato-intermitente" class="hidden">
                            <label for="doc_contrato_intermitente" class="block text-sm font-medium text-gray-700 mb-2">Contrato Intermitente</label>
                            <input type="date" id="doc_contrato_intermitente" name="doc_contrato_intermitente"
                                value="{{ old('doc_contrato_intermitente', $funcionario->doc_contrato_intermitente ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="doc_aso" class="block text-sm font-medium text-gray-700 mb-2">ASO (Atestado Saúde Ocup.)</label>
                            <input type="date" id="doc_aso" name="doc_aso"
                                value="{{ old('doc_aso', $funcionario->doc_aso ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_ordem_servico" class="block text-sm font-medium text-gray-700 mb-2">Ordem de Serviço</label>
                            <input type="date" id="doc_ordem_servico" name="doc_ordem_servico"
                                value="{{ old('doc_ordem_servico', $funcionario->doc_ordem_servico ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_ficha_epi" class="block text-sm font-medium text-gray-700 mb-2">Ficha de EPI</label>
                            <input type="date" id="doc_ficha_epi" name="doc_ficha_epi"
                                value="{{ old('doc_ficha_epi', $funcionario->doc_ficha_epi ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr06" class="block text-sm font-medium text-gray-700 mb-2">NR 06</label>
                            <input type="date" id="doc_nr06" name="doc_nr06"
                                value="{{ old('doc_nr06', $funcionario->doc_nr06 ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr10" class="block text-sm font-medium text-gray-700 mb-2">NR 10</label>
                            <input type="date" id="doc_nr10" name="doc_nr10"
                                value="{{ old('doc_nr10', $funcionario->doc_nr10 ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 2 anos</p>
                        </div>

                        <div>
                            <label for="doc_nr12" class="block text-sm font-medium text-gray-700 mb-2">NR 12</label>
                            <input type="date" id="doc_nr12" name="doc_nr12"
                                value="{{ old('doc_nr12', $funcionario->doc_nr12 ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr18" class="block text-sm font-medium text-gray-700 mb-2">NR 18</label>
                            <input type="date" id="doc_nr18" name="doc_nr18"
                                value="{{ old('doc_nr18', $funcionario->doc_nr18 ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 2 anos</p>
                        </div>

                        <div>
                            <label for="doc_nr35" class="block text-sm font-medium text-gray-700 mb-2">NR 35</label>
                            <input type="date" id="doc_nr35" name="doc_nr35"
                                value="{{ old('doc_nr35', $funcionario->doc_nr35 ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 2 anos</p>
                        </div>
                    </div>
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Informações Adicionais</h2>
                <div>
                    <label for="sexo" class="block text-sm font-medium text-gray-700 mb-2">Sexo</label>
                    <select name="sexo" id="sexo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione o sexo</option>
                        <option value="Masculino" {{ old('sexo', $funcionario->sexo ?? '') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ old('sexo', $funcionario->sexo ?? '') == 'Feminino' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>
                <div>
                    <label for="estado_civil" class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                    <select name="estado_civil" id="estado_civil" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione o estado civil</option>
                        <option value="Solteiro" {{ old('estado_civil', $funcionario->estado_civil ?? '') == 'Solteiro' ? 'selected' : '' }}>Solteiro</option>
                        <option value="Casado" {{ old('estado_civil', $funcionario->estado_civil ?? '') == 'Casado' ? 'selected' : '' }}>Casado</option>
                        <option value="Divorciado" {{ old('estado_civil', $funcionario->estado_civil ?? '') == 'Divorciado' ? 'selected' : '' }}>Divorciado</option>
                        <option value="Viúvo" {{ old('estado_civil', $funcionario->estado_civil ?? '') == 'Viúvo' ? 'selected' : '' }}>Viúvo</option>
                    </select>
                </div>
                <div>
                    <label for="numero_filhos" class="block text-sm font-medium text-gray-700 mb-2">Número de Filhos</label>
                    <input type="number" id="numero_filhos" name="numero_filhos"
                        value="{{ old('numero_filhos', $funcionario->numero_filhos ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: 2">
                </div>
                <div>
                    <label for="estado_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Estado de Nascimento</label>
                    <input type="text" id="estado_nascimento" name="estado_nascimento"
                        value="{{ old('estado_nascimento', $funcionario->estado_nascimento ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Paraná">
                </div>
                <div>
                    <label for="cidade_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Cidade de Nascimento</label>
                    <input type="text" id="cidade_nascimento" name="cidade_nascimento"
                        value="{{ old('cidade_nascimento', $funcionario->cidade_nascimento ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Curitiba">
                </div>
                <div>
                    <label for="ativo" class="block text-sm font-medium text-gray-700 mb-2">Ativo</label>
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" id="ativo" name="ativo" value="1" {{ old('ativo', $funcionario->ativo ?? 0) ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Endereço</h2>
                <div>
                    <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                    <div class="flex gap-2">
                        <input type="text" id="cep" name="cep" 
                            value="{{ old('cep', $funcionario->cep ?? '') }}" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Ex: 01001-000">
                        
                        <button type="button" id="btn-buscar-cep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" title="Buscar CEP">
                            <i class="bi bi-search"></i>        
                        </button>
                    </div>
                </div>
                <div>
                    <label for="logradouro" class="block text-sm font-medium text-gray-700 mb-2">Logradouro</label>
                    <input type="text" id="logradouro" name="logradouro"
                        value="{{ old('logradouro', $funcionario->logradouro ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Av. Paulista">
                </div>
                <div>
                    <label for="bairro" class="block text-sm font-medium text-gray-700 mb-2">Bairro</label>
                    <input type="text" id="bairro" name="bairro"
                        value="{{ old('bairro', $funcionario->bairro ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Centro">
                </div>
                <div>
                    <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                    <input type="text" id="numero" name="numero"
                        value="{{ old('numero', $funcionario->numero ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 123">
                </div>
                <div>
                    <label for="cidade" class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                    <input type="text" id="cidade" name="cidade"
                        value="{{ old('cidade', $funcionario->cidade ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Curitiba">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <input type="text" id="estado" name="estado"
                        value="{{ old('estado', $funcionario->estado ?? '') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Paraná">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Outros</h2>
                <div>
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Digite suas observações aqui...">{{ old('observacoes', $funcionario->observacoes ?? '') }}</textarea>
                </div>
                <div>
                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
            <div class="flex justify-end mt-10 pt-6">
                <a href="{{ route('rh.funcionarios.index') }}"
                    class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg mr-4">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg">
                    Atualizar Funcionário
                </button>
            </div>
        </form>

    

@endsection