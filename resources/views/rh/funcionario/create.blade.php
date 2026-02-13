@extends('layouts.main')

@section('title', 'Magserv | Novo Funcionário')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Cadastrar Novo Funcionário</h1>
            <p class="text-gray-600 mt-1">Preencha os dados abaixo para adicionar um novo funcionário ao sistema.</p>
        </div>
        <a href="{{ route('rh.funcionarios.index') }}"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg">
            Voltar para a Lista
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('rh.funcionarios.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                
                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-2">Informações Pessoais</h2>
                <div>
                    <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo <span class="text-red-500">*</span></label>
                    <input type="text" id="nome" name="nome"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: João da Silva" required>
                </div>
                <div>
                    <label for="cpf" class="block text-sm font-medium text-gray-700 mb-2">CPF</label>
                    <input type="text" id="cpf" name="cpf"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 123.456.789-00">
                </div>
                <div>
                    <label for="rg" class="block text-sm font-medium text-gray-700 mb-2">RG</label>
                    <input type="text" id="rg" name="rg"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 12.345.678-9">
                </div>
                <div>
                    <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                    <input type="tel" id="telefone" name="telefone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: (11) 99999-8888">
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" id="email" name="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: exemplo@dominio.com">
                </div>
                <div>
                    <label for="data_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Data de Nascimento</label>
                    <input type="date" id="data_nascimento" name="data_nascimento"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Dados contratuais</h2>
                <div>
                    <label for="cargo" class="block text-sm font-medium text-gray-700 mb-2">Cargo</label>
                    <input type="text" id="cargo" name="cargo"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Técnico de Manutenção">
                </div>
                <div>
                    <label for="tipo_contrato" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Contrato <span class="text-red-500">*</span></label>
                    <select name="tipo_contrato" id="tipo_contrato"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                        <option value="">Selecione o tipo de contrato</option>
                        <option value="Fixo">CLT (Fixo)</option>
                        <option value="PJ">PJ</option>
                        <option value="Estagio">Estágio</option>
                        <option value="Intermitente">Intermitente</option>
                    </select>
                </div>
                <div>
                    <label for="data_admissao" class="block text-sm font-medium text-gray-700 mb-2">Data de Admissão</label>
                    <input type="date" id="data_admissao" name="data_admissao"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label for="data_demissao" class="block text-sm font-medium text-gray-700 mb-2">Data de Demissão</label>
                    <input type="date" id="data_demissao" name="data_demissao"
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
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="doc_aso" class="block text-sm font-medium text-gray-700 mb-2">ASO (Atestado Saúde Ocup.)</label>
                            <input type="date" id="doc_aso" name="doc_aso"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_ordem_servico" class="block text-sm font-medium text-gray-700 mb-2">Ordem de Serviço</label>
                            <input type="date" id="doc_ordem_servico" name="doc_ordem_servico"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_ficha_epi" class="block text-sm font-medium text-gray-700 mb-2">Ficha de EPI</label>
                            <input type="date" id="doc_ficha_epi" name="doc_ficha_epi"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr06" class="block text-sm font-medium text-gray-700 mb-2">NR 06</label>
                            <input type="date" id="doc_nr06" name="doc_nr06"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr10" class="block text-sm font-medium text-gray-700 mb-2">NR 10</label>
                            <input type="date" id="doc_nr10" name="doc_nr10"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 2 anos</p>
                        </div>

                        <div>
                            <label for="doc_nr12" class="block text-sm font-medium text-gray-700 mb-2">NR 12</label>
                            <input type="date" id="doc_nr12" name="doc_nr12"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 1 ano</p>
                        </div>

                        <div>
                            <label for="doc_nr18" class="block text-sm font-medium text-gray-700 mb-2">NR 18</label>
                            <input type="date" id="doc_nr18" name="doc_nr18"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-blue-600 font-semibold mt-1">Validade: 2 anos</p>
                        </div>

                        <div>
                            <label for="doc_nr35" class="block text-sm font-medium text-gray-700 mb-2">NR 35</label>
                            <input type="date" id="doc_nr35" name="doc_nr35"
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
                        <option value="Masculino">Masculino</option>
                        <option value="Feminino">Feminino</option>
                    </select>
                </div>
                <div>
                    <label for="estado_civil" class="block text-sm font-medium text-gray-700 mb-2">Estado Civil</label>
                    <select name="estado_civil" id="estado_civil" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Selecione o estado civil</option>
                        <option value="Solteiro">Solteiro</option>
                        <option value="Casado">Casado</option>
                        <option value="Divorciado">Divorciado</option>
                        <option value="Viúvo">Viúvo</option>
                    </select>
                </div>
                <div>
                    <label for="numero_filhos" class="block text-sm font-medium text-gray-700 mb-2">Número de Filhos</label>
                    <input type="number" id="numero_filhos" name="numero_filhos"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: 2">
                </div>
                <div>
                    <label for="estado_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Estado de Nascimento</label>
                    <input type="text" id="estado_nascimento" name="estado_nascimento"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Paraná">
                </div>
                <div>
                    <label for="cidade_nascimento" class="block text-sm font-medium text-gray-700 mb-2">Cidade de Nascimento</label>
                    <input type="text" id="cidade_nascimento" name="cidade_nascimento"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" placeholder="Ex: Curitiba">
                </div>
                <div>
                    <label for="ativo" class="block text-sm font-medium text-gray-700 mb-2">Ativo</label>
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" id="ativo" name="ativo" value="1" checked class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Endereço</h2>
                <div>
                    <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                    <div class="flex gap-2">
                        <input type="text" id="cep" name="cep" 
                            value="{{ isset($funcionario) ? $funcionario->cep : '' }}" 
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
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Av. Paulista">
                </div>
                <div>
                    <label for="bairro" class="block text-sm font-medium text-gray-700 mb-2">Bairro</label>
                    <input type="text" id="bairro" name="bairro"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Centro">
                </div>
                <div>
                    <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                    <input type="text" id="numero" name="numero"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: 123">
                </div>
                <div>
                    <label for="cidade" class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                    <input type="text" id="cidade" name="cidade"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Curitiba">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <input type="text" id="estado" name="estado"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Ex: Paraná">
                </div>

                <h2 class="col-span-full text-xl font-semibold text-gray-800 mt-6">Outros</h2>
                <div>
                    <label for="observacoes" class="block text-sm font-medium text-gray-700 mb-2">Observações</label>
                    <textarea id="observacoes" name="observacoes" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Digite suas observações aqui..."></textarea>
                </div>
                <div>
                    <label for="foto_perfil" class="block text-sm font-medium text-gray-700 mb-2">Foto de Perfil</label>
                    <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
            </div>
            <div class="flex justify-end mt-10 pt-6">
                <a href="{{ route('rh.funcionarios.index') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg mr-4">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg">
                    Salvar Funcionário
                </button>
            </div>
        </form>
    </div>

@endsection