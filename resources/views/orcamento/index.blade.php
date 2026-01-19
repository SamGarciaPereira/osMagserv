    @extends('layouts.main')

    @section('title', 'Magserv | Orçamentos')

    @section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lista de Orçamentos</h1>
            <p class="text-gray-600 mt-1">Gerencie todas as propostas enviadas.</p>
        </div>
        <a href="{{ route('orcamentos.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Cadastrar Novo Orçamento
        </a>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
        <form method="GET" action="{{ route('orcamentos.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            
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
                    <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="Em Andamento" {{ request('status') == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="Em Validação" {{ request('status') == 'Em Validação' ? 'selected' : '' }}>Em Validação</option>
                    <option value="Validado" {{ request('status') == 'Validado' ? 'selected' : '' }}>Validado</option>
                    <option value="Enviado" {{ request('status') == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                    <option value="Aprovado" {{ request('status') == 'Aprovado' ? 'selected' : '' }}>Aprovado</option>
                </select>
            </div>

            <div class="md:col-span-3">
                <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
                <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
                    <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
                    <option value="maior_valor" {{ request('ordem') == 'maior_valor' ? 'selected' : '' }}>Maior Valor</option>
                    <option value="menor_valor" {{ request('ordem') == 'menor_valor' ? 'selected' : '' }}>Menor Valor</option>
                    <option value="envio" {{ request('ordem') == 'envio' ? 'selected' : '' }}>Data Envio</option>
                    <option value="aprovacao" {{ request('ordem') == 'aprovacao' ? 'selected' : '' }}>Data Aprovação</option>
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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nº Proposta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demanda</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($orcamentos as $orcamento)
                        <tr>
                            <td class="px-6 py-4">
                                <button class="toggle-details-btn text-gray-500 hover:text-gray-800" data-target-id="{{ $orcamento->id }}">
                                    <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($orcamento->numero_proposta)
                                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">
                                        {{ $orcamento->numero_proposta }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="max-w-[226px] truncate py-4 whitespace-nowrap text-sm text-gray-500" title="{{ $orcamento->cliente->nome ?? 'N/A' }}">{{ $orcamento->cliente->nome ?? 'N/A' }}</td>
                            <td class="max-w-[265px] truncate px-6 py-4 whitespace-nowrap text-sm text-gray-500" title="{{ $orcamento->escopo ? : 'Não definido'}}">{{ $orcamento->escopo ? : 'Não definido'}}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <x-status-badge :status="$orcamento->status" />
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('orcamentos.edit', $orcamento->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="bi bi-pencil-fill text-base"></i>
                                    </a>
                                    <form action="{{ route('orcamentos.destroy', $orcamento->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                            <i class="bi bi-trash-fill text-base"></i>
                                        </button>
                                    </form>
                                    <button onclick="openAnexoModal({{ $orcamento->id }}, '{{ addslashes($orcamento->escopo) }}')"  class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                                        <i class="bi bi-paperclip text-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $orcamento->id }}" class="hidden details-row">
                            <td colspan="6" class="px-6 py-4 bg-gray-50">
                                <div class="flex flex-col md:flex-row gap-6 items-start">
                                    <div class="flex flex-col gap-2 md:w-1/3 text-gray-500">
                                        <p><strong>Valor: </strong>R$ {{ number_format($orcamento->valor, 2, ',', '.') }}</p>
                                        <p><strong>Data de Solicitação:</strong> {{ $orcamento->data_solicitacao ? \Carbon\Carbon::parse($orcamento->data_solicitacao)->format('d/m/Y') : 'Não definida' }}</p>
                                        <p><strong>Data de Envio:</strong> {{ $orcamento->data_envio ? \Carbon\Carbon::parse($orcamento->data_envio)->format('d/m/Y') : 'Não definida' }}</p>
                                        <p><strong>Data de Aprovação:</strong> {{ $orcamento->data_aprovacao ? \Carbon\Carbon::parse($orcamento->data_aprovacao)->format('d/m/Y') : 'Não definida' }}</p>
                                        <p><strong>Revisão:</strong> {{ $orcamento->revisao }}</p>
                                        <p><strong>Comentários:</strong></p>
                                        <p class="whitespace-pre-line {{ $orcamento->comentario ? 'text-blue-800 font-bold' : 'text-gray-500 italic' }}">{{ $orcamento->comentario ?: 'Nenhum comentário adicionado.' }}
                                        </p>
                                        @if($orcamento->status == 'Pendente' || $orcamento->status == 'Em Andamento')
                                        <p><strong>Checklist:</strong></p>
                                        <ul>
                                            @php
                                                $tarefas = collect($orcamento->checklist ?? [])->where('completed', false);
                                            @endphp

                                            @forelse($tarefas as $tarefa)
                                                <li class="text-orange-700 font-bold">- {{ $tarefa['text'] }}</li>
                                            @empty
                                                <li class="text-green-600 font-bold">
                                                    <i class="bi bi-check-all"></i> Tudo em dia!
                                                </li>
                                            @endforelse
                                        </ul>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 md:w-1/3">
                                        <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                                            <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                                        </h4>
                                        
                                        @if($orcamento->anexos && $orcamento->anexos->count() > 0)
                                            <div class="grid gap-3" style="grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));">
                                                @foreach($orcamento->anexos as $anexo)
                                                    <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                                                        <div class="flex items-center overflow-hidden">
                                                            @if(Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
                                                                <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl mr-3 flex-shrink-0"></i>
                                                            @else
                                                                <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl mr-3 flex-shrink-0"></i>
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
                                            <p class="text-sm text-gray-500 italic">Nenhum anexo encontrado para este orçamento.</p>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 md:w-1/3">
                                        @if($orcamento->last_user_id)
                                            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                                <div class="gap-2 mb-1">
                                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 rounded text-xs font-bold border border-blue-200 mb-2 uppercase">
                                                        <i class="bi bi-clock-history mr-1"></i> Última Alteração
                                                    </span>
                                                </div>
                                                <p class="text-sm mb-1 text-gray-600">
                                                    {{ $orcamento->updated_at->format('d/m/Y') }} às {{ $orcamento->updated_at->format('H:i') }}
                                                </p>
                                                <p class="text-sm text-gray-600">
                                                    Por: <strong class="text-blue-800">{{ $orcamento->editor->name ?? 'Sistema' }}</strong>
                                                </p>
                                            </div>
                                        @endif      
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nenhum orçamento cadastrado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    </div> <x-modal model-type="App\Models\Orcamento" />

    @endsection
