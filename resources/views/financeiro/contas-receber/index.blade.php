@extends('layouts.main')

@section('title', 'Magserv | Contas a Receber')

@section('content')

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Contas a Receber</h1>
        <p class="text-gray-600 mt-1">Gerencie todos os recebimentos pendentes e efetuados.</p>
    </div>
    <a href="{{ route('financeiro.contas-receber.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
        <i class="bi bi-plus-lg mr-2"></i>
        Nova Conta a Receber
    </a>
</div>

<div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
    <form method="GET" action="{{ route('financeiro.contas-receber.index') }}" class="grid grid-cols-1 md:grid-cols-20 gap-4 items-end">
        
        <div class="md:col-span-5">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="bi bi-search text-gray-400"></i>
                </div>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="NF, Cliente, Descrição..."
                       class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="md:col-span-3">
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="">Todos</option>
                <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                <option value="Pago" {{ request('status') == 'Pago' ? 'selected' : '' }}>Pago</option>
                <option value="Atrasado" {{ request('status') == 'Atrasado' ? 'selected' : '' }}>Atrasado</option>
            </select>
        </div>

        <div class="md:col-span-3">
            <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
            <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                <option value="maior_valor" {{ request('ordem') == 'maior_valor' ? 'selected' : '' }}>Maior Valor</option>
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
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Venc.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data Receb.</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($contasReceber as $conta) 
                    <tr>
                        <td class="px-6 py-4">
                            <button class="toggle-details-btn text-gray-500 hover:text-gray-800 transition-colors"
                            data-target-id="{{ $conta->id }}">
                                <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                            </button>
                        </td>
                        
                        {{-- CORREÇÃO AQUI: Exibe apenas a NF da conta ($conta->nf), removendo a referência ao processo --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            {{ $conta->nf ?? '-' }}
                        </td>

                        <td class="max-w-[150px] truncate py-4 whitespace-nowrap text-sm text-gray-900" title="{{ $conta->cliente->nome ?? 'N/A' }}">{{ $conta->cliente->nome ?? 'N/A' }}</td>
                        <td class="max-w-[295px] truncate px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900" title="{{ $conta->processo->orcamento->numero_proposta ?? $conta->descricao }}">
                            @if ($conta->processo && $conta->processo->orcamento)
                                {{ $conta->descricao }}: <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">{{ $conta->processo->orcamento->numero_proposta }} </span>
                            @else
                                {{ 'N/A' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $conta->data_vencimento ? \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $conta->data_recebimento ? \Carbon\Carbon::parse($conta->data_recebimento)->format('d/m/Y') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <x-status-badge :status="$conta->status" />
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('financeiro.contas-receber.edit', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                    <i class="bi bi-pencil-fill text-base"></i>
                                </a>
                                <form action="{{ route('financeiro.contas-receber.destroy', $conta->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                        <i class="bi bi-trash-fill text-base"></i>
                                    </button>
                                </form>
                                <button onclick="openAnexoModal({{ $conta->id }}, '{{ $conta->descricao }}')" 
                                        class="text-gray-500 hover:text-blue-600 mr-3" 
                                        title="Anexar Arquivo">
                                            <i class="bi bi-paperclip text-lg"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <tr id="details-{{ $conta->id }}" class="hidden details-row bg-gray-50 border-b border-gray-200">
                        <td colspan="8" class="px-6 py-4">
                            <div class="flex flex-col md:flex-row gap-6 justify-between items-start">
                                <div class="flex-1 w-full">
                                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center">
                                        <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                    </h4>
                                    @if($conta->anexos && $conta->anexos->count() > 0)
                                        <div class="flex flex-wrap gap-3">
                                            @foreach($conta->anexos as $anexo)
                                                <div class="flex-1 min-w-[200px] bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                    <div class="flex items-center overflow-hidden min-w-0">
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
                                                    <div class="flex items-center gap-2 ml-2 flex-shrink-0">
                                                        <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" 
                                                        class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Ver">
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
                                        <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para esta conta.</p>
                                    @endif
                                </div>
                                <div class="flex-shrink-0 md:w-64">
                                    @if($conta->last_user_id)
                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                            <div class="mb-2">
                                                <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold border border-blue-200 uppercase inline-flex items-center">
                                                    <i class="bi bi-clock-history mr-1"></i> Última Alteração
                                                </span>
                                            </div>
                                            <p class="text-sm mb-1 text-gray-600">
                                                {{ $conta->updated_at->format('d/m/Y') }} às {{ $conta->updated_at->format('H:i') }}
                                            </p>
                                            <p class="text-sm text-gray-600">
                                                Por: <strong class="text-blue-800">{{ $conta->editor->name ?? 'Sistema' }}</strong>
                                            </p>
                                        </div>
                                    @endif      
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                            Nenhuma conta a receber encontrada.
                        </td>
                    </tr>
                @endforelse 
            </tbody>
        </table>
    </div>
</div>

</div> <x-modal model-type="App\Models\ContasReceber" />

@endsection