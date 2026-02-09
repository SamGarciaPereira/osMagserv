@extends('layouts.main')

@section('title', 'Magserv | Contratos')

@section('content')

    @php
        $labelsContrato = [
            'ativo' => 'Ativo',
            'data_inicio' => 'Data Início Contrato',
            'data_fim' => 'Data Fim Contrato',
        ];
    @endphp

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Contratos de Manutenção</h1>
            <p class="text-gray-600 mt-1">Visualize e gerencie todos os contratos de manutenção.</p>
        </div>
        <a href=" {{ route('contratos.create') }} "
            class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Cadastrar Contrato
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="{{ route('contratos.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <div class="md:col-span-7">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                    placeholder="Cliente, Número do contrato..."
                    class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
            <div class="md:col-span-4">
                <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                    <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                    <option value="data_inicio" {{ request('ordem') == 'data_inicio' ? 'selected' : '' }}>Data Início Contrato</option>
                    <option value="data_fim" {{ request('ordem') == 'data_fim' ? 'selected' : '' }}>Data Fim Contrato</option>
                </select>
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
                        <th class="px-6 py-3"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contrato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Início</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Fim</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ativo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($contratos as $contrato)
                    <tr>
                        <td class="px-6 py-4">
                            <button class="toggle-details-btn text-gray-500 hover:text-gray-800" data-target-id="{{ $contrato->id }}">
                                <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                            </button>
                        </td>
                        <td class="max-w-[226px] truncate py-4 whitespace-nowrap" title="{{ $contrato->clientes->pluck('nome')->join(', ') ?? 'N/A' }}">
                            <div class="flex flex-col gap-1">
                                @foreach($contrato->clientes as $cli)
                                    <div class="text-sm font-bold text-purple-700 flex items-center">
                                        <i class="bi bi-building-fill mr-2"></i>
                                        {{ $cli->nome }}
                                    </div>
                                    @if($cli->filiais->count() > 0)
                                        <div class="text-xs text-gray-500 ml-6">
                                            <i class="bi bi-arrow-return-right"></i>
                                            Abrange {{ $cli->filiais->count() }} filiais
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">
                                    {{ $contrato->numero_contrato ?? "N/A" }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $contrato->data_inicio ? $contrato->data_inicio->format('d/m/Y') : 'Não definido' }}
                            </div>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $contrato->data_fim ? $contrato->data_fim->format('d/m/Y') : 'Não definido' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @php
                                switch ($contrato->ativo) {
                                    case 1:
                                        $statusClass = 'bg-green-100 text-green-800';
                                        break;
                                    case 0:
                                        $statusClass = 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        $statusClass = 'bg-gray-100 text-gray-800';
                                }
                            @endphp
                            @if($contrato->ativo)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                Sim
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                                Não
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('contratos.edit', $contrato->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <i class="bi bi-pencil-fill text-base"></i>
                                </a>
                                <form action="{{ route('contratos.destroy', $contrato->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                        <i class="bi bi-trash-fill text-base"></i>
                                    </button>
                                </form>
                                <button onclick="openAnexoModal({{ $contrato->id }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                    <i class="bi bi-paperclip text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr id="details-{{ $contrato->id }}" class="hidden details-row">
                        <td colspan="8" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                
                                <div class="flex flex-col gap-3 md:w-1/3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center">
                                        <i class="bi bi-shop mr-2"></i> Filiais Cobertas
                                    </h4>
                                    
                                    <div class="bg-white border border-gray-200 rounded-lg p-3 shadow-sm">
                                        @php
                                            // Coleta todas as filiais de todas as matrizes vinculadas
                                            $todasFiliais = $contrato->clientes->flatMap->filiais;
                                        @endphp

                                        @if($todasFiliais->count() > 0)
                                            <ul class="space-y-2">
                                                @foreach($todasFiliais as $filial)
                                                    <li class="flex items-center justify-between p-2 hover:bg-gray-50 rounded-md transition-colors">
                                                        <div class="flex items-center">
                                                            <i class="bi bi-arrow-return-right text-gray-400 mr-2 text-sm"></i>
                                                            
                                                            <a href="{{ route('clientes.edit', $filial->id) }}" class="text-sm font-medium text-gray-700 hover:text-blue-600 hover:underline">
                                                                {{ $filial->nome }}
                                                            </a>
                                                        </div>
                                                        <span class="text-[10px] uppercase font-bold text-blue-700 bg-blue-50 border border-blue-100 px-2 py-0.5 rounded-full">
                                                            Filial
                                                        </span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                            
                                            <div class="mt-3 pt-2 border-t border-gray-100 text-xs text-gray-400 text-right">
                                                Total de {{ $todasFiliais->count() }} filial(is) coberta(s)
                                            </div>
                                        @else
                                            <div class="p-4 text-center">
                                                <p class="text-sm text-gray-500 italic">Esta Matriz não possui filiais cadastradas.</p>
                                                <p class="text-xs text-gray-400 mt-1">O contrato cobre apenas a sede.</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 md:w-1/3">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center">
                                        <i class="bi bi-folder2-open mr-2"></i> Arquivos do Contrato
                                    </h4>
                                    
                                    @if($contrato->anexos && $contrato->anexos->count() > 0)
                                        <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
                                            @foreach($contrato->anexos as $anexo)
                                                <div class="bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between shadow-sm hover:shadow-md transition group">
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
                                                            <p class="text-[10px] text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center gap-1 ml-2">
                                                        <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 rounded"><i class="bi bi-eye-fill"></i></a>
                                                        <a href="{{ route('anexos.download', $anexo->id) }}" class="p-1.5 text-gray-500 hover:text-green-600 rounded"><i class="bi bi-download"></i></a>
                                                        <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 rounded"><i class="bi bi-trash"></i></button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-white border border-gray-200 border-dashed rounded-lg p-6 text-center">
                                            <p class="text-sm text-gray-400">Nenhum anexo encontrado.</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-2 md:w-1/3">
                                    <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                                        <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                                            <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                                                <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                                            </span>
                                            <button type="button" 
                                                    onclick='openGeneralHistoryModal(@json($contrato->history), @json($labelsContrato))' 
                                                    class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                                                Ver Completo
                                            </button>
                                        </div>

                                        @if($contrato->history && $contrato->history->count() > 0)
                                            <div class="space-y-3">
                                                @foreach($contrato->history->take(3) as $activity)
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
                                        
                                        @if($contrato->last_user_id && (!$contrato->history || $contrato->history->count() == 0))
                                            <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                                                Última ação por: <strong>{{ $contrato->editor->name ?? 'Sistema' }}</strong><br>
                                                em {{ $contrato->updated_at->format('d/m/Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>                        
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Nenhum contrato manutenção cadastrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div> <x-modal-history />
</div> <x-modal model-type="App\Models\Contrato" />

@endsection

