@extends('layouts.main')

@section('title', 'Magserv | Funcionários')

@section('content')

    @php
        $labelsFuncionario = [
            'nome'              => 'Nome Completo',
            'cpf'               => 'CPF',
            'rg'                => 'RG',
            'data_nascimento'   => 'Data de Nascimento',
            'estado_nascimento' => 'Naturalidade (Estado)',
            'cidade_nascimento' => 'Naturalidade (Cidade)',
            'estado_civil'      => 'Estado Civil',
            'sexo'              => 'Sexo',
            'numero_filhos'     => 'Nº de Filhos',
            'foto_perfil'       => 'Foto de Perfil',
            'email'             => 'E-mail',
            'telefone'          => 'Telefone',
            'cep'               => 'CEP',
            'logradouro'        => 'Logradouro',
            'numero'            => 'Número',
            'bairro'            => 'Bairro',
            'cidade'            => 'Cidade',
            'estado'            => 'Estado (UF)',
            'cargo'             => 'Cargo',
            'tipo_contrato'     => 'Tipo de Contrato',
            'data_admissao'     => 'Data de Admissão',
            'data_demissao'     => 'Data de Demissão',
            'ativo'             => 'Status (Ativo/Inativo)',
            'observacoes'       => 'Observações',
            'doc_aso'           => 'Data de Emissão ASO',
            'doc_ordem_servico' => 'Data de Emissão Ordem de Serviço',
            'doc_ficha_epi'     => 'Data de Emissão Ficha EPI',
            'doc_nr06'          => 'Data de Emissão NR06',
            'doc_nr10'          => 'Data de Emissão NR10',
            'doc_nr12'          => 'Data de Emissão NR12',
            'doc_nr18'          => 'Data de Emissão NR18',
            'doc_nr35'          => 'Data de Emissão NR35',
            'doc_contrato_intermitente' => 'Data de Emissão Contrato Intermitente',
            'status_documentos' => 'Status dos Documentos',
        ];
    @endphp

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lista de Funcionários</h1>
            <p class="text-gray-600 mt-1">Gerencie todos os funcionários cadastrados no sistema.</p>
        </div>
        <a href="{{ route('rh.funcionarios.create') }}"
            class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Cadastrar Novo Funcionário
        </a>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            Funcionários Fixos
        </h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Admissão</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcionariosFixos as $funcionario)
                            <tr>
                                <td class="px-6 py-4">
                                    <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                        data-target-id="{{ $funcionario->id }}">
                                        <i
                                            class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $funcionario->nome }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->telefone ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->cargo ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->ativo ? 'Sim' : 'Não' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('rh.funcionarios.edit', $funcionario->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <i class="bi bi-pencil-fill text-base"></i>
                                        </a>
                                        <form action="{{ route('rh.funcionarios.destroy', $funcionario->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja remover este funcionário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                                <i class="bi bi-trash-fill text-base"></i>
                                            </button>
                                        </form>
                                        <button onclick="openAnexoModal({{ $funcionario->id }}, {{ json_encode($funcionario->escopo ?? '') }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                            <i class="bi bi-paperclip text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="details-{{ $funcionario->id }}" class="hidden details-row">
                                <td colspan="8" class="px-6 py-2 bg-gray-50">
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-3 gap-2 max-h-40 overflow-auto border-b border-gray-200">
                                        <div class="space-y-1">
                                            <p><strong>CPF:</strong> {{ $funcionario->cpf ?? 'Não informado' }}</p>
                                            <p><strong>RG:</strong> {{ $funcionario->rg ?? 'Não informado' }}</p>
                                            <p><strong>Data de Nascimento:</strong>
                                                {{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                            <p><strong>Endereço:</strong> {{ $funcionario->logradouro ?? 'N/A' }},
                                            {{ $funcionario->numero ?? 'N/A' }}, {{ $funcionario->bairro ?? 'N/A' }}, {{ $funcionario->cidade ?? 'N/A' }},
                                            {{ $funcionario->estado ?? 'N/A' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>E-mail:</strong> {{ $funcionario->email ?? 'Não informado' }}</p>
                                            <p><strong>Tipo de Contrato:</strong> {{ $funcionario->tipo_contrato ?? 'Não informado' }}</p>
                                            <p><strong>Data de Demissão:</strong>
                                                {{ $funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>Sexo:</strong> {{ $funcionario->sexo ?? 'Não informado' }}</p>
                                            <p><strong>Estado Civil:</strong> {{ $funcionario->estado_civil ?? 'Não informado' }}</p>
                                            <p><strong>Nº Filhos:</strong> {{ $funcionario->numero_filhos ?? 'Não informado' }}</p>
                                            <p><strong>Observações:</strong> {{ $funcionario->observacoes ?? 'Nenhuma' }}</p>
                                        </div>
                                    </div>
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                                        <div class="space-y-1">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                                <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                            </h4>
                                            
                                            @if($funcionario->anexos && $funcionario->anexos->count() > 0)
                                                <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                    @foreach($funcionario->anexos as $anexo)
                                                        <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                            <div class="flex items-center overflow-hidden">
                                                                @php
                                                                    $ext = strtolower(pathinfo($anexo->nome_original, PATHINFO_EXTENSION));
                                                                @endphp

                                                                @if($ext === 'pdf')
                                                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                                                    <i class="bi bi-file-earmark-excel-fill text-green-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                    <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @else
                                                                    <i class="bi bi-file-earmark-fill text-gray-500 text-xl mr-3 flex-shrink-0"></i>
                                                                @endif
                                                                
                                                                <div class="truncate">
                                                                    <p class="text-sm font-medium text-gray-700 max-w-[226px] truncate" title="{{ $anexo->nome_original }}">
                                                                        {{ $anexo->nome_original }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 ml-2">
                                                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" 
                                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" 
                                                                title="Visualizar">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                                <a href="{{ route('anexos.download', $anexo->id) }}" 
                                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" 
                                                                title="Baixar">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este funcionário.</p>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                                <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                                    <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                        <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                                                    </span>
                                                    <button type="button" 
                                                            onclick='openGeneralHistoryModal(@json($funcionario->history), @json($labelsFuncionario))' 
                                                            class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                        Ver Completo
                                                    </button>
                                                </div>

                                                @if($funcionario->history && $funcionario->history->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($funcionario->history->take(3) as $activity)
                                                            <div class="flex flex-col text-xs text-gray-600 bg-white bg-opacity-60 p-1.5 rounded border border-blue-100">
                                                                <div class="flex justify-between font-semibold text-gray-700">
                                                                    <span class="text-blue-900">
                                                                        {{ $activity->version }}ª Versão 
                                                                        ({{ $activity->event == 'created' ? 'Criação' : ($activity->event == 'updated' ? 'Edição' : 'Remoção') }})
                                                                    </span>
                                                                    <span class="text-gray-500 text-[10px]">
                                                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m H:i') }}
                                                                    </span>
                                                                </div>
                                                                <div class="mt-0.5 truncate">
                                                                    Por: <span class="font-medium text-gray-800">{{ $activity->user->name ?? 'Sistema' }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center justify-center h-20 text-gray-400">
                                                        <i class="bi bi-clock text-xl mb-1 opacity-50"></i>
                                                        <p class="text-xs italic">Nenhum histórico detalhado.</p>
                                                    </div>
                                                @endif
                                                
                                                @if($funcionario->last_user_id && (!$funcionario->history || $funcionario->history->count() == 0))
                                                    <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                        Última ação por: <strong>{{ $funcionario->editor->name ?? 'Sistema' }}</strong><br>
                                                        em {{ $funcionario->updated_at->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            Funcionários PJ
        </h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Admissão</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcionariosPJ as $funcionario)
                            <tr>
                                <td class="px-6 py-4">
                                    <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                        data-target-id="{{ $funcionario->id }}">
                                        <i
                                            class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $funcionario->nome }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->telefone ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->cargo ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->ativo ? 'Sim' : 'Não' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('rh.funcionarios.edit', $funcionario->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <i class="bi bi-pencil-fill text-base"></i>
                                        </a>
                                        <form action="{{ route('rh.funcionarios.destroy', $funcionario->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja remover este funcionário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                                <i class="bi bi-trash-fill text-base"></i>
                                            </button>
                                        </form>
                                        <button onclick="openAnexoModal({{ $funcionario->id }}, {{ json_encode($funcionario->escopo ?? '') }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                            <i class="bi bi-paperclip text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="details-{{ $funcionario->id }}" class="hidden details-row">
                                <td colspan="8" class="px-6 py-2 bg-gray-50">
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-3 gap-2 max-h-40 overflow-auto border-b border-gray-200">
                                        <div class="space-y-1">
                                            <p><strong>CPF:</strong> {{ $funcionario->cpf ?? 'Não informado' }}</p>
                                            <p><strong>RG:</strong> {{ $funcionario->rg ?? 'Não informado' }}</p>
                                            <p><strong>Data de Nascimento:</strong>
                                                {{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                            <p><strong>Endereço:</strong> {{ $funcionario->logradouro ?? 'N/A' }},
                                            {{ $funcionario->numero ?? 'N/A' }}, {{ $funcionario->bairro ?? 'N/A' }}, {{ $funcionario->cidade ?? 'N/A' }},
                                            {{ $funcionario->estado ?? 'N/A' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>E-mail:</strong> {{ $funcionario->email ?? 'Não informado' }}</p>
                                            <p><strong>Tipo de Contrato:</strong> {{ $funcionario->tipo_contrato ?? 'Não informado' }}</p>
                                            <p><strong>Data de Demissão:</strong>
                                                {{ $funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>Sexo:</strong> {{ $funcionario->sexo ?? 'Não informado' }}</p>
                                            <p><strong>Estado Civil:</strong> {{ $funcionario->estado_civil ?? 'Não informado' }}</p>
                                            <p><strong>Nº Filhos:</strong> {{ $funcionario->numero_filhos ?? 'Não informado' }}</p>
                                            <p><strong>Observações:</strong> {{ $funcionario->observacoes ?? 'Nenhuma' }}</p>
                                        </div>
                                    </div>
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                                        <div class="space-y-1">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                                <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                            </h4>
                                            
                                            @if($funcionario->anexos && $funcionario->anexos->count() > 0)
                                                <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                    @foreach($funcionario->anexos as $anexo)
                                                        <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                            <div class="flex items-center overflow-hidden">
                                                                @php
                                                                    $ext = strtolower(pathinfo($anexo->nome_original, PATHINFO_EXTENSION));
                                                                @endphp

                                                                @if($ext === 'pdf')
                                                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                                                    <i class="bi bi-file-earmark-excel-fill text-green-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                    <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @else
                                                                    <i class="bi bi-file-earmark-fill text-gray-500 text-xl mr-3 flex-shrink-0"></i>
                                                                @endif
                                                                
                                                                <div class="truncate">
                                                                    <p class="text-sm font-medium text-gray-700 max-w-[226px] truncate" title="{{ $anexo->nome_original }}">
                                                                        {{ $anexo->nome_original }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 ml-2">
                                                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" 
                                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" 
                                                                title="Visualizar">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                                <a href="{{ route('anexos.download', $anexo->id) }}" 
                                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" 
                                                                title="Baixar">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este funcionário.</p>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                                <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                                    <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                        <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                                                    </span>
                                                    <button type="button" 
                                                            onclick='openGeneralHistoryModal(@json($funcionario->history), @json($labelsFuncionario))' 
                                                            class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                        Ver Completo
                                                    </button>
                                                </div>

                                                @if($funcionario->history && $funcionario->history->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($funcionario->history->take(3) as $activity)
                                                            <div class="flex flex-col text-xs text-gray-600 bg-white bg-opacity-60 p-1.5 rounded border border-blue-100">
                                                                <div class="flex justify-between font-semibold text-gray-700">
                                                                    <span class="text-blue-900">
                                                                        {{ $activity->version }}ª Versão 
                                                                        ({{ $activity->event == 'created' ? 'Criação' : ($activity->event == 'updated' ? 'Edição' : 'Remoção') }})
                                                                    </span>
                                                                    <span class="text-gray-500 text-[10px]">
                                                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m H:i') }}
                                                                    </span>
                                                                </div>
                                                                <div class="mt-0.5 truncate">
                                                                    Por: <span class="font-medium text-gray-800">{{ $activity->user->name ?? 'Sistema' }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center justify-center h-20 text-gray-400">
                                                        <i class="bi bi-clock text-xl mb-1 opacity-50"></i>
                                                        <p class="text-xs italic">Nenhum histórico detalhado.</p>
                                                    </div>
                                                @endif
                                                
                                                @if($funcionario->last_user_id && (!$funcionario->history || $funcionario->history->count() == 0))
                                                    <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                        Última ação por: <strong>{{ $funcionario->editor->name ?? 'Sistema' }}</strong><br>
                                                        em {{ $funcionario->updated_at->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            Estágiários
        </h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Admissão</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcionariosEstagio as $funcionario)
                            <tr>
                                <td class="px-6 py-4">
                                    <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                        data-target-id="{{ $funcionario->id }}">
                                        <i
                                            class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $funcionario->nome }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->telefone ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->cargo ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->ativo ? 'Sim' : 'Não' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('rh.funcionarios.edit', $funcionario->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <i class="bi bi-pencil-fill text-base"></i>
                                        </a>
                                        <form action="{{ route('rh.funcionarios.destroy', $funcionario->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja remover este funcionário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                                <i class="bi bi-trash-fill text-base"></i>
                                            </button>
                                        </form>
                                        <button onclick="openAnexoModal({{ $funcionario->id }}, {{ json_encode($funcionario->escopo ?? '') }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                            <i class="bi bi-paperclip text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="details-{{ $funcionario->id }}" class="hidden details-row">
                                <td colspan="8" class="px-6 py-2 bg-gray-50">
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-3 gap-2 max-h-40 overflow-auto border-b border-gray-200">
                                        <div class="space-y-1">
                                            <p><strong>CPF:</strong> {{ $funcionario->cpf ?? 'Não informado' }}</p>
                                            <p><strong>RG:</strong> {{ $funcionario->rg ?? 'Não informado' }}</p>
                                            <p><strong>Data de Nascimento:</strong>
                                                {{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                            <p><strong>Endereço:</strong> {{ $funcionario->logradouro ?? 'N/A' }},
                                            {{ $funcionario->numero ?? 'N/A' }}, {{ $funcionario->bairro ?? 'N/A' }}, {{ $funcionario->cidade ?? 'N/A' }},
                                            {{ $funcionario->estado ?? 'N/A' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>E-mail:</strong> {{ $funcionario->email ?? 'Não informado' }}</p>
                                            <p><strong>Tipo de Contrato:</strong> {{ $funcionario->tipo_contrato ?? 'Não informado' }}</p>
                                            <p><strong>Data de Demissão:</strong>
                                                {{ $funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>Sexo:</strong> {{ $funcionario->sexo ?? 'Não informado' }}</p>
                                            <p><strong>Estado Civil:</strong> {{ $funcionario->estado_civil ?? 'Não informado' }}</p>
                                            <p><strong>Nº Filhos:</strong> {{ $funcionario->numero_filhos ?? 'Não informado' }}</p>
                                            <p><strong>Observações:</strong> {{ $funcionario->observacoes ?? 'Nenhuma' }}</p>
                                        </div>
                                    </div>
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                                        <div class="space-y-1">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                                <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                            </h4>
                                            
                                            @if($funcionario->anexos && $funcionario->anexos->count() > 0)
                                                <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                    @foreach($funcionario->anexos as $anexo)
                                                        <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                            <div class="flex items-center overflow-hidden">
                                                                @php
                                                                    $ext = strtolower(pathinfo($anexo->nome_original, PATHINFO_EXTENSION));
                                                                @endphp

                                                                @if($ext === 'pdf')
                                                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                                                    <i class="bi bi-file-earmark-excel-fill text-green-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                    <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @else
                                                                    <i class="bi bi-file-earmark-fill text-gray-500 text-xl mr-3 flex-shrink-0"></i>
                                                                @endif
                                                                
                                                                <div class="truncate">
                                                                    <p class="text-sm font-medium text-gray-700 max-w-[226px] truncate" title="{{ $anexo->nome_original }}">
                                                                        {{ $anexo->nome_original }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 ml-2">
                                                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" 
                                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" 
                                                                title="Visualizar">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                                <a href="{{ route('anexos.download', $anexo->id) }}" 
                                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" 
                                                                title="Baixar">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este funcionário.</p>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                                <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                                    <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                        <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                                                    </span>
                                                    <button type="button" 
                                                            onclick='openGeneralHistoryModal(@json($funcionario->history), @json($labelsFuncionario))' 
                                                            class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                        Ver Completo
                                                    </button>
                                                </div>

                                                @if($funcionario->history && $funcionario->history->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($funcionario->history->take(3) as $activity)
                                                            <div class="flex flex-col text-xs text-gray-600 bg-white bg-opacity-60 p-1.5 rounded border border-blue-100">
                                                                <div class="flex justify-between font-semibold text-gray-700">
                                                                    <span class="text-blue-900">
                                                                        {{ $activity->version }}ª Versão 
                                                                        ({{ $activity->event == 'created' ? 'Criação' : ($activity->event == 'updated' ? 'Edição' : 'Remoção') }})
                                                                    </span>
                                                                    <span class="text-gray-500 text-[10px]">
                                                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m H:i') }}
                                                                    </span>
                                                                </div>
                                                                <div class="mt-0.5 truncate">
                                                                    Por: <span class="font-medium text-gray-800">{{ $activity->user->name ?? 'Sistema' }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center justify-center h-20 text-gray-400">
                                                        <i class="bi bi-clock text-xl mb-1 opacity-50"></i>
                                                        <p class="text-xs italic">Nenhum histórico detalhado.</p>
                                                    </div>
                                                @endif
                                                
                                                @if($funcionario->last_user_id && (!$funcionario->history || $funcionario->history->count() == 0))
                                                    <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                        Última ação por: <strong>{{ $funcionario->editor->name ?? 'Sistema' }}</strong><br>
                                                        em {{ $funcionario->updated_at->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            Funcionários Intermitentes
        </h2>
        <div class="bg-white p-8 rounded-lg shadow-md">
            <div class="overflow-x-auto">
                <table class="w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data de Admissão</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($funcionariosIntermitentes as $funcionario)
                            <tr>
                                <td class="px-6 py-4">
                                    <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                        data-target-id="{{ $funcionario->id }}">
                                        <i
                                            class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                    </button>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $funcionario->nome }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->telefone ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->cargo ?? 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado' }}</div>
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-500">{{ $funcionario->ativo ? 'Sim' : 'Não' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('rh.funcionarios.edit', $funcionario->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                            <i class="bi bi-pencil-fill text-base"></i>
                                        </a>
                                        <form action="{{ route('rh.funcionarios.destroy', $funcionario->id) }}" method="POST"
                                            onsubmit="return confirm('Tem certeza que deseja remover este funcionário?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                                <i class="bi bi-trash-fill text-base"></i>
                                            </button>
                                        </form>
                                        <button onclick="openAnexoModal({{ $funcionario->id }}, {{ json_encode($funcionario->escopo ?? '') }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                            <i class="bi bi-paperclip text-base"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr id="details-{{ $funcionario->id }}" class="hidden details-row">
                                <td colspan="8" class="px-6 py-2 bg-gray-50">
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-3 gap-2 max-h-40 overflow-auto border-b border-gray-200">
                                        <div class="space-y-1">
                                            <p><strong>CPF:</strong> {{ $funcionario->cpf ?? 'Não informado' }}</p>
                                            <p><strong>RG:</strong> {{ $funcionario->rg ?? 'Não informado' }}</p>
                                            <p><strong>Data de Nascimento:</strong>
                                                {{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                            <p><strong>Endereço:</strong> {{ $funcionario->logradouro ?? 'N/A' }},
                                            {{ $funcionario->numero ?? 'N/A' }}, {{ $funcionario->bairro ?? 'N/A' }}, {{ $funcionario->cidade ?? 'N/A' }},
                                            {{ $funcionario->estado ?? 'N/A' }}</p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>E-mail:</strong> {{ $funcionario->email ?? 'Não informado' }}</p>
                                            <p><strong>Tipo de Contrato:</strong> {{ $funcionario->tipo_contrato ?? 'Não informado' }}</p>
                                            <p><strong>Data de Demissão:</strong>
                                                {{ $funcionario->data_demissao ? $funcionario->data_demissao->format('d/m/Y') : 'Não informado' }}
                                            </p>
                                        </div>
                                        <div class="space-y-1">
                                            <p><strong>Sexo:</strong> {{ $funcionario->sexo ?? 'Não informado' }}</p>
                                            <p><strong>Estado Civil:</strong> {{ $funcionario->estado_civil ?? 'Não informado' }}</p>
                                            <p><strong>Nº Filhos:</strong> {{ $funcionario->numero_filhos ?? 'Não informado' }}</p>
                                            <p><strong>Observações:</strong> {{ $funcionario->observacoes ?? 'Nenhuma' }}</p>
                                        </div>
                                    </div>
                                    <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                                        <div class="space-y-1">
                                            <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                                <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                            </h4>
                                            
                                            @if($funcionario->anexos && $funcionario->anexos->count() > 0)
                                                <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                    @foreach($funcionario->anexos as $anexo)
                                                        <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                            <div class="flex items-center overflow-hidden">
                                                                @php
                                                                    $ext = strtolower(pathinfo($anexo->nome_original, PATHINFO_EXTENSION));
                                                                @endphp

                                                                @if($ext === 'pdf')
                                                                    <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['xls', 'xlsx', 'csv']))
                                                                    <i class="bi bi-file-earmark-excel-fill text-green-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @elseif(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                    <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
                                                                
                                                                @else
                                                                    <i class="bi bi-file-earmark-fill text-gray-500 text-xl mr-3 flex-shrink-0"></i>
                                                                @endif
                                                                
                                                                <div class="truncate">
                                                                    <p class="text-sm font-medium text-gray-700 max-w-[226px] truncate" title="{{ $anexo->nome_original }}">
                                                                        {{ $anexo->nome_original }}
                                                                    </p>
                                                                    <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                                </div>
                                                            </div>

                                                            <div class="flex items-center gap-2 ml-2">
                                                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" 
                                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" 
                                                                title="Visualizar">
                                                                    <i class="bi bi-eye-fill"></i>
                                                                </a>
                                                                <a href="{{ route('anexos.download', $anexo->id) }}" 
                                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" 
                                                                title="Baixar">
                                                                    <i class="bi bi-download"></i>
                                                                </a>
                                                                <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition" title="Excluir">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este funcionário.</p>
                                            @endif
                                        </div>
                                        <div class="space-y-1">
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                                <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                                    <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                        <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                                                    </span>
                                                    <button type="button" 
                                                            onclick='openGeneralHistoryModal(@json($funcionario->history), @json($labelsFuncionario))' 
                                                            class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                        Ver Completo
                                                    </button>
                                                </div>

                                                @if($funcionario->history && $funcionario->history->count() > 0)
                                                    <div class="space-y-3">
                                                        @foreach($funcionario->history->take(3) as $activity)
                                                            <div class="flex flex-col text-xs text-gray-600 bg-white bg-opacity-60 p-1.5 rounded border border-blue-100">
                                                                <div class="flex justify-between font-semibold text-gray-700">
                                                                    <span class="text-blue-900">
                                                                        {{ $activity->version }}ª Versão 
                                                                        ({{ $activity->event == 'created' ? 'Criação' : ($activity->event == 'updated' ? 'Edição' : 'Remoção') }})
                                                                    </span>
                                                                    <span class="text-gray-500 text-[10px]">
                                                                        {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m H:i') }}
                                                                    </span>
                                                                </div>
                                                                <div class="mt-0.5 truncate">
                                                                    Por: <span class="font-medium text-gray-800">{{ $activity->user->name ?? 'Sistema' }}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="flex flex-col items-center justify-center h-20 text-gray-400">
                                                        <i class="bi bi-clock text-xl mb-1 opacity-50"></i>
                                                        <p class="text-xs italic">Nenhum histórico detalhado.</p>
                                                    </div>
                                                @endif
                                                
                                                @if($funcionario->last_user_id && (!$funcionario->history || $funcionario->history->count() == 0))
                                                    <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                        Última ação por: <strong>{{ $funcionario->editor->name ?? 'Sistema' }}</strong><br>
                                                        em {{ $funcionario->updated_at->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif
                                            </div>
        
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                    Nenhum funcionário encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
        
    </div> <x-modal-history />
    </div> <x-modal model-type="App\Models\Funcionario" />
@endsection