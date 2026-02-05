@extends('layouts.main')

@section('title', 'Magserv | Processos')

@section('content')

    @php
        $labelsProcesso = [
            'nf' => 'NF',
            'status' => 'Status Atual',
            'escopo' => 'Demanda',
            'valor' => 'Valor',
            'numero_proposta' => 'Nº Proposta',
        ];
    @endphp

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Gestão de Processos</h1>
            <p class="text-gray-600 mt-1">Visualize e gerencie todos os processos em andamento.</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="{{ route('processos.index') }}" class="grid grid-cols-1 md:grid-cols-20 gap-4 items-end">
            
            <div class="md:col-span-5">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Nº Proposta, Cliente ou Demanda..."
                           class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="md:col-span-3">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="">Todos</option>
                    <option value="Em Aberto" {{ request('status') == 'Em Aberto' ? 'selected' : '' }}>Em Aberto</option>
                    <option value="Finalizado" {{ request('status') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="Faturado" {{ request('status') == 'Faturado' ? 'selected' : '' }}>Faturado</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                    <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                    <option value="maior_valor" {{ request('ordem') == 'maior_valor' ? 'selected' : '' }}>Maior Valor</option>
                    <option value="menor_valor" {{ request('ordem') == 'menor_valor' ? 'selected' : '' }}>Menor Valor</option>
                </select>
            </div>

            <div class="md:col-span-4">
                <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">De</label>
                <input type="month" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div class="md:col-span-4">
                <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Até</label>
                <input type="month" name="data_fim" id="data_fim" value="{{ request('data_fim') }}" 
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NF</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                   @forelse ($processos as $processo)
                    <tr>
                        <td class="px-6 py-4">
                            <button class="toggle-details-btn text-gray-500 hover:text-gray-800" data-target-id="{{ $processo->id }}">
                                <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">{{ $processo->nf ?? 'N/A' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($processo->orcamento->numero_proposta)
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">
                                    {{ $processo->orcamento->numero_proposta }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">N/A</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $processo->orcamento->cliente->nome ?? 'N/A' }} </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">R$ {{ number_format($processo->orcamento->valor ?? 0, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusLabel = $processo->status;
                                $statusClass = 'bg-gray-100 text-gray-800';
                                $detalheFinanceiro = '';

                                if ($processo->status == 'Faturado') {
                                    $totalParcelas = $processo->contasReceber->sum('valor');
                                    $valorOrcamento = $processo->orcamento->valor;
                        
                                    if (abs($totalParcelas - $valorOrcamento) > 0.01) {
                                        $statusLabel = 'Faturado (Parcial)';
                                        $statusClass = 'bg-orange-100 text-orange-800'; // Laranja para chamar atenção
                                        $porcentagem = ($valorOrcamento > 0) ? ($totalParcelas / $valorOrcamento) * 100 : 0;
                                        $detalheFinanceiro = number_format($porcentagem, 0) . '% (' . number_format($totalParcelas, 2, ',', '.') . ')';
                                    } else {
                                        $statusLabel = 'Faturado (Total)';
                                        $statusClass = 'bg-green-100 text-green-800';
                                    }
                                } elseif ($processo->status == 'Em Aberto') {
                                    $statusClass = 'bg-yellow-100 text-yellow-800';
                                } elseif ($processo->status == 'Finalizado') {
                                    $statusClass = 'bg-blue-100 text-blue-800';
                                }
                            @endphp

                            <div class="flex flex-col items-start">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                                @if($detalheFinanceiro)
                                    <span class="text-[10px] text-gray-500 mt-1 ml-1" title="Valor Faturado">
                                        {{ $detalheFinanceiro }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('processos.edit', $processo->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Gerenciar Processo">
                                <i class="bi bi-pencil-fill"></i>
                            </a>
                            <button onclick="openAnexoModal({{ $processo->id }})" class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                    <i class="bi bi-paperclip text-lg"></i>
                            </button>
                        </td>
                    </tr>
                    <tr id="details-{{ $processo->id }}" class="hidden details-row">
                        <td colspan="8" class="px-6 py-4 bg-gray-50">
                            <div class="text-gray-500 p-2 mb-4 border-b border-gray-200">
                                <p><strong>Demanda:</strong><br>{{ $processo->orcamento->escopo ? : 'Não definido'}} </p>
                            </div>
                            <div class="flex flex-col md:flex-row gap-6 items-start">     
                                <div class="flex flex-col gap-2 md:w-1/3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                    </h4>
                                    
                                    @if($processo->anexos && $processo->anexos->count() > 0)
                                        <div class="flex flex-col gap-3">
                                            @foreach($processo->anexos as $anexo)
                                                <div class="w-full bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                    <div class="flex items-center gap-3 overflow-hidden">
                                                        @if(Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
                                                            <i class="bi bi-file-earmark-pdf-fill text-red-500 text-2xl flex-shrink-0"></i>
                                                        @else
                                                            <i class="bi bi-file-earmark-image-fill text-blue-500 text-2xl flex-shrink-0"></i>
                                                        @endif

                                                        <div class="min-w-0">
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
                                                        class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" title="Baixar">
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
                                        <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este processo.</p>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 md:w-1/3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                        <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados do Orçamento
                                    </h4>
                                    
                                    @if($processo->orcamento && $processo->orcamento->anexos->count() > 0)
                                        <div class="flex flex-col gap-3">
                                            @foreach($processo->orcamento->anexos as $anexo)
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
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-sm text-gray-400 italic">O orçamento vinculado não possui anexos.</p>
                                    @endif
                                </div>
                                <div class="p-2 border-t border-gray-100 flex flex-col gap-2 md:w-1/3">
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                        <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                            <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                <i class="bi bi-clock-history mr-1"></i> Histórico
                                            </span>
                                            <button type="button" 
                                                    onclick='openGeneralHistoryModal(@json($processo->history), @json($labelsProcesso))' 
                                                    class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                Ver Completo
                                            </button>
                                        </div>

                                        @if($processo->history && $processo->history->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($processo->history->take(3) as $activity)
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
                                        @if($processo->last_user_id && (!$processo->history || $processo->history->count() == 0))
                                            <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                Última ação por: <strong>{{ $processo->editor->name ?? 'Sistema' }}</strong><br>
                                                em {{ $processo->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                </div>                 
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">Nenhum processo iniciado.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-modal-history />
    </div> <x-modal model-type="App\Models\Processo" />

@endsection