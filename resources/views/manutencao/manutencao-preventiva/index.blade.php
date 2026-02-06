@extends('layouts.main')

@section('title', 'Magserv | Manutenções Preventivas')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manutenções Preventivas</h1>
            <p class="text-gray-600 mt-1">Visualize e gerencie todas as manutenções preventivas.</p>
        </div>
        <a href="{{ route('manutencoes.preventiva.create') }}"
            class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Agendar Preventiva
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="{{ route('manutencoes.preventiva.index') }}" class="grid grid-cols-1 md:grid-cols-16 gap-4 items-end">
            <div class="md:col-span-4">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Cliente, Cód. OS, Solicitante..."
                           class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="md:col-span-2">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">Todos</option>
                    <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="Agendada" {{ request('status') == 'Agendada' ? 'selected' : '' }}>Agendada</option>
                    <option value="Em Andamento" {{ request('status') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="Concluída" {{ request('status') == 'Concluída' ? 'selected' : '' }}>Concluída</option>
                    <option value="Cancelada" {{ request('status') == 'Cancelada' ? 'selected' : '' }}>Cancelada</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                    <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                    <option value="data_inicio" {{ request('ordem') == 'data_inicio' ? 'selected' : '' }}>Data Início Atendimento</option>
                    <option value="data_fim" {{ request('ordem') == 'data_fim' ? 'selected' : '' }}>Data Fim Atendimento</option>
                </select>
            </div>
            <div class="md:col-span-3">
                <label for="data_inicio_filtro" class="block text-sm font-medium text-gray-700 mb-1">De</label>
                <input type="month" name="data_inicio_filtro" id="data_inicio_filtro" value="{{ request('data_inicio_filtro') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-3">
                <label for="data_fim_filtro" class="block text-sm font-medium text-gray-700 mb-1">Até</label>
                <input type="month" name="data_fim_filtro" id="data_fim_filtro" value="{{ request('data_fim_filtro') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded-md text-sm hover:bg-blue-700 transition" title="Filtrar">
                    <i class="bi bi-filter"></i>
                </button>
            </div>
        </form>
    </div>

    @if (session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3"></th> <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OS</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Início</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Fim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($manutencoes as $manutencao)
                    <tr>
                        <td class="px-6 py-4">
                            <button class="toggle-details-btn text-gray-500 hover:text-gray-800" data-target-id="{{ $manutencao->id }}">
                                <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                            </button>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $manutencao->cliente->nome ?? 'N/A' }}</div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($manutencao->chamado)
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">
                                    {{ $manutencao->chamado }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Gerando...</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $manutencao->data_inicio_atendimento ? $manutencao->data_inicio_atendimento->format('d/m/Y') : 'Não definido' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $manutencao->data_fim_atendimento ? $manutencao->data_fim_atendimento->format('d/m/Y') : 'Não definido' }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <x-status-badge :status="$manutencao->status" />
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('manutencoes.preventiva.edit', $manutencao->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <i class="bi bi-pencil-fill text-base"></i>
                                </a>
                                <form action="{{ route('manutencoes.destroy', $manutencao->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Excluir">
                                        <i class="bi bi-trash-fill text-base"></i>
                                    </button>
                                </form>
                                <button onclick="openAnexoModal({{ $manutencao->id }}, '{{ $manutencao->descricao }}')" class="text-gray-500 hover:text-blue-600" title="Anexar">
                                    <i class="bi bi-paperclip text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <tr id="details-{{ $manutencao->id }}" class="hidden details-row">
                        <td colspan="7" class="px-6 py-4 bg-gray-50">
                            <div class="flex flex-col md:flex-row gap-6 items-start">
                                
                                <div class="flex flex-col gap-4 md:w-1/3 text-gray-500 text-sm">
                                    <div>
                                        <p class="font-bold text-gray-700 mb-1">Descrição:</p>
                                        <p>{{ $manutencao->descricao ?: 'Não definido' }}</p>
                                    </div>

                                    @php
                                        $contratoVigente = $manutencao->cliente ? $manutencao->cliente->contratoAtivo() : null;
                                    @endphp

                                    <div class="pt-4 border-t border-gray-200">
                                        <p class="font-bold text-gray-700 mb-2">
                                            <i class="bi bi-file-text mr-1"></i> Contrato Vigente
                                        </p>
                                        
                                        @if($contratoVigente)
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3 shadow-sm">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-xs font-bold uppercase text-blue-600">Nº Contrato</span>
                                                    <span class="px-2 py-0.5 rounded-full bg-green-100 text-green-800 text-xs border border-green-200 font-bold">Ativo</span>
                                                </div>
                                                <p class="text-lg font-bold text-blue-900 font-mono mb-1 tracking-tight">
                                                    {{ $contratoVigente->numero_contrato }}
                                                </p>
                                                <p class="text-xs text-blue-700 mb-1">
                                                    Validade: 
                                                    {{ $contratoVigente->data_inicio ? \Carbon\Carbon::parse($contratoVigente->data_inicio)->format('d/m/Y') : '?' }} a 
                                                    {{ $contratoVigente->data_fim ? \Carbon\Carbon::parse($contratoVigente->data_fim)->format('d/m/Y') : '?' }}
                                                </p>
                                                @if($manutencao->cliente->matriz_id && !$manutencao->cliente->contratos->contains($contratoVigente->id))
                                                    <p class="text-[10px] text-blue-500 italic border-t border-blue-100 pt-1 mt-1">
                                                        <i class="bi bi-arrow-return-right mr-1"></i> Contrato da Matriz
                                                    </p>
                                                @endif
                                            </div>
                                        @else
                                            <div class="bg-gray-100 border border-gray-200 rounded-md p-3 text-center">
                                                <p class="text-xs text-gray-500 italic">Sem contrato ativo.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-2 md:w-1/3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                    </h4>
                                    
                                    @if($manutencao->anexos && $manutencao->anexos->count() > 0)
                                        <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                                            @foreach($manutencao->anexos as $anexo)
                                                <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                    <div class="flex items-center overflow-hidden">
                                                        @if(Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
                                                            <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
                                                        @endif
                                                        <div class="truncate">
                                                            <p class="text-sm font-medium text-gray-700 truncate" title="{{ $anexo->nome_original }}">
                                                                {{ $anexo->nome_original }}
                                                            </p>
                                                            <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2 ml-2">
                                                        <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 rounded"><i class="bi bi-eye-fill"></i></a>
                                                        <a href="{{ route('anexos.download', $anexo->id) }}" class="p-1.5 text-gray-500 hover:text-green-600 rounded"><i class="bi bi-download"></i></a>
                                                        <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded transition"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado.</p>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 md:w-1/3">
                                    @if($manutencao->last_user_id)
                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                            <div class="gap-2 mb-1">
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold border border-blue-200 mb-2 uppercase">
                                                    <i class="bi bi-clock-history mr-1"></i> Última Alteração
                                                </span>
                                            </div>
                                            <p class="text-sm mb-1 text-gray-600">
                                                {{ $manutencao->updated_at->format('d/m/Y') }} às {{ $manutencao->updated_at->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Por: <strong class="text-blue-800">{{ $manutencao->editor->name ?? 'Sistema' }}</strong>
                                            </p>
                                        </div>
                                    @endif      
                                </div>
                            </div>
                        </td>                        
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Nenhuma manutenção preventiva cadastrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal model-type="App\Models\Manutencao" />

@endsection