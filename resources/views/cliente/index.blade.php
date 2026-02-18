@extends('layouts.main')

@section('title', 'Magserv | Clientes')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lista de Clientes</h1>
            <p class="text-gray-600 mt-1">Gerencie todos os clientes cadastrados no sistema.</p>
        </div>
        <a href="{{ route('clientes.create') }}"
            class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Cadastrar Novo Cliente
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="{{ route('clientes.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

            <div class="md:col-span-7">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400"></i>
                    </div>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="Nome, Razão Social, CNPJ, Responsável ou E-mail..."
                        class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="md:col-span-4">
                <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                    <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                </select>
            </div>

            <div class="md:col-span-1">
                <button type="submit"
                    class="bg-blue-600 text-white w-full py-2 rounded-md text-sm hover:bg-blue-700 transition"
                    title="Filtrar">
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome /
                            Razão Social</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CNPJ</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Responsável</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td class="px-6 py-4">
                                <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                    data-target-id="{{ $cliente->id }}">
                                    <i
                                        class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $cliente->nome }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $cliente->documento }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $cliente->responsavel ?? 'Não informado' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $cliente->telefone ?? 'Não informado' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('clientes.edit', $cliente->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="bi bi-pencil-fill text-base"></i>
                                    </a>
                                    <form action="{{ route('clientes.destroy', $cliente->id) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja remover este cliente?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                            <i class="bi bi-trash-fill text-base"></i>
                                        </button>
                                    </form>
                                    <button onclick="openAnexoModal({{ $cliente->id }}, {{ json_encode($cliente->anexos ?? '') }})"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                        <i class="bi bi-paperclip text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $cliente->id }}" class="hidden details-row">
                            <td colspan="6" class="px-6 py-2 bg-gray-50">
                                <div class="p-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2 max-h-40 overflow-auto">
                                    <div class="space-y-1">
                                        <p class="mb-0"><strong>Endereço:</strong> {{ $cliente->logradouro ?? 'N/A' }},
                                            {{ $cliente->numero ?? 'N/A' }}, {{ $cliente->bairro ?? 'N/A' }}, {{ $cliente->cidade ?? 'N/A' }},
                                            {{ $cliente->estado ?? 'N/A' }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <p class="mb-0"><strong>E-mail:</strong> {{ $cliente->email ?? 'Não informado'}}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col md:flex-row gap-6 items-start">
                                    <div class="flex flex-col gap-2 md:w-1/3">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                            <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                        </h4>
                                        
                                        @if($cliente->anexos && $cliente->anexos->count() > 0)
                                            <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                @foreach($cliente->anexos as $anexo)
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
                                    <div class="flex flex-col gap-2 md:w-1/3">
                                        @if($cliente->matriz)
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold border border-blue-200">
                                                        FILIAL
                                                    </span>
                                                </div>
                                                <p class="text-sm text-gray-600">
                                                    Vinculada à Matriz: 
                                                    <a href="{{ route('clientes.edit', $cliente->matriz->id) }}" class="font-bold text-blue-600 hover:underline">
                                                        {{ $cliente->matriz->nome }}
                                                    </a>
                                                </p>
                                            </div>

                                        @elseif($cliente->filiais->count() > 0)
                                            <div class="bg-purple-50 border border-purple-200 rounded-md p-3">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-800 rounded text-xs font-bold border border-purple-200">
                                                        MATRIZ
                                                    </span>
                                                    <span class="text-xs text-purple-600 font-medium">
                                                        {{ $cliente->filiais->count() }} unidade(s) vinculada(s)
                                                    </span>
                                                </div>
                                                
                                                <div class="pl-2 border-l-2 border-purple-200">
                                                    <p class="text-xs text-gray-500 mb-1">Filiais:</p>
                                                    <ul class="text-sm text-gray-700 space-y-1">
                                                        @foreach($cliente->filiais as $filial)
                                                            <li class="flex items-center">
                                                                <i class="bi bi-arrow-return-right text-gray-400 mr-2 text-xs"></i>
                                                                {{ $filial->nome }}
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            </div>

                                        @else
                                            <div class="flex items-center gap-2">
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs font-bold border border-gray-200">
                                                    UNIDADE ÚNICA
                                                </span>
                                                <span class="text-xs text-gray-400 italic">Não possui vínculos.</span>
                                            </div>
                                        @endif
                                    </div>    
                                    <div class="flex flex-col gap-2 md:w-1/3">
                                        @if($cliente->last_user_id)
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                <div class="gap-2 mb-1">
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold border border-blue-200 mb-2 uppercase">
                                                        <i class="bi bi-clock-history mr-1"></i> Última Alteração
                                                    </span>
                                                </div>
                                                <p class="text-sm mb-1 text-gray-600">
                                                    {{ $cliente->updated_at->format('d/m/Y') }} às {{ $cliente->updated_at->format('H:i') }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Por: <strong class="text-blue-800">{{ $cliente->editor->name ?? 'Sistema' }}</strong>
                                                </p>
                                            </div>
                                        @endif      
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                Nenhum cliente cadastrado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div> <x-modal model-type="App\Models\Cliente" />
@endsection