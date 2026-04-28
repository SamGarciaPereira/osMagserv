@extends('layouts.main')

@section('title', 'Magserv | Contas a Pagar')

@section('content')

  @php
    $labelsContasPagar = [
        'descricao' => 'Descrição',
        'valor' => 'Valor',
        'data_vencimento' => 'Data de Vencimento',
        'data_pagamento' => 'Data de Pagamento',
        'status' => 'Status',
        'fornecedor' => 'Fornecedor',
        'danfe' => 'DANFE',
    ];
  @endphp

  <div class="flex justify-between items-center mb-8">
    <div>
      <h1 class="text-3xl font-bold text-gray-900">Contas a Pagar</h1>
      <p class="text-gray-600 mt-1">Gerencie todas as contas a pagar pendentes e efetuadas.</p>
    </div>
    <a href="{{ route('financeiro.contas-pagar.create') }}" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
      <i class="bi bi-plus-lg mr-2"></i>
      Nova Conta a Pagar
    </a>
  </div>

  <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
    <form method="GET" action="{{ route('financeiro.contas-pagar.index') }}" id="filter-form" class="grid grid-cols-1 md:grid-cols-20 gap-4 items-end">
      <input type="hidden" name="filtro_aplicado" value="1">

      <div class="md:col-span-5">
        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
        <div class="relative">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <i class="bi bi-search text-gray-400"></i>
          </div>
          <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Fornecedor, Descrição..." class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>
      </div>

      <div class="md:col-span-3 relative dropdown-container">
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <button type="button" class="dropdown-btn w-full px-3 py-[9px] border border-gray-300 rounded-md text-sm bg-white flex justify-between items-center hover:bg-gray-50 transition-all">
          <span class="truncate">{{ count($statusSelecionados) }} selecionados</span>
          <i class="bi bi-chevron-down text-gray-400 ml-2"></i>
        </button>

        <div class="dropdown-menu hidden absolute z-50 mt-1 w-60 bg-white border border-gray-200 rounded-md shadow-xl p-2 left-0">
          <div class="flex justify-center gap-4 items-center border-b border-gray-100 pb-2 mb-2">
            <button type="button" class="btn-select-all flex items-center gap-1 px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-500 rounded text-[10px] uppercase font-bold hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition-all shadow-sm">
              Todos
            </button>
            <button type="button" class="btn-clear-all flex items-center gap-1 px-2.5 py-1 bg-gray-50 border border-gray-200 text-gray-500 rounded text-[10px] uppercase font-bold hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all shadow-sm">
              Limpar
            </button>
          </div>

          <div class="flex flex-col gap-1 max-h-64 overflow-y-auto">
            @foreach (['Pendente', 'Pago', 'Atrasado'] as $status)
              <label class="flex items-center justify-between px-3 py-2 hover:bg-blue-50 rounded-md cursor-pointer group transition-all">
                <div class="flex items-center">
                  <input type="checkbox" name="status[]" value="{{ $status }}" class="peer hidden status-checkbox" {{ in_array($status, $statusSelecionados) ? 'checked' : '' }}>
                  <span class="text-sm text-gray-600 peer-checked:text-blue-700 peer-checked:font-bold transition-all">{{ $status }}</span>
                </div>
                <i class="bi bi-check2 text-blue-600 font-bold opacity-0 peer-checked:opacity-100 transition-opacity"></i>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      @php
        $ordemAtual = request('ordem', 'recentes');
        $opcoesOrdem = [
            'recentes' => 'Recentes',
            'antigos' => 'Antigos',
            'maior_valor' => 'Maior Valor',
        ];
      @endphp
      <div class="md:col-span-3 relative dropdown-container">
        <label class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
        <button type="button" class="dropdown-btn w-full px-3 py-[9px] border border-gray-300 rounded-md text-sm bg-white flex justify-between items-center hover:bg-gray-50 transition-all">
          <span class="truncate">{{ $opcoesOrdem[$ordemAtual] ?? 'Ordenar' }}</span>
          <i class="bi bi-chevron-down text-gray-400 ml-2"></i>
        </button>

        <div class="dropdown-menu hidden absolute z-50 mt-1 w-56 bg-white border border-gray-200 rounded-md shadow-xl p-2 left-0">
          <div class="flex flex-col gap-1">
            @foreach ($opcoesOrdem as $val => $label)
              <label class="flex items-center justify-between px-3 py-2 hover:bg-blue-50 rounded-md cursor-pointer group transition-all">
                <div class="flex items-center">
                  <input type="radio" name="ordem" value="{{ $val }}" class="peer hidden" {{ $ordemAtual == $val ? 'checked' : '' }}>
                  <span class="text-sm text-gray-600 peer-checked:text-blue-700 peer-checked:font-bold transition-all">{{ $label }}</span>
                </div>
                <i class="bi bi-check2 text-blue-600 font-bold opacity-0 peer-checked:opacity-100 transition-opacity"></i>
              </label>
            @endforeach
          </div>
          <div class="mt-2 pt-2 border-t border-gray-100">
            <button type="submit" class="w-full bg-blue-600 text-white py-1.5 rounded-md text-xs font-bold hover:bg-blue-700 transition">Aplicar</button>
          </div>
        </div>
      </div>

      <div class="md:col-span-4">
        <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">De</label>
        <input type="month" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
      </div>

      <div class="md:col-span-4">
        <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Até</label>
        <input type="month" name="data_fim" id="data_fim" value="{{ request('data_fim') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
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

  <div class="mb-8">
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
      Contas Fixas / Recorrentes
    </h2>
    <div class="bg-white p-8 rounded-lg shadow-md">
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DANFE</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($contasFixas as $conta)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <button class="toggle-details-btn text-gray-500 hover:text-gray-800 transition-colors" data-target-id="{{ $conta->id }}">
                    <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                  </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $conta->danfe ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $conta->fornecedor ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $conta->descricao }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $conta->data_vencimento ? \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $conta->data_pagamento ? \Carbon\Carbon::parse($conta->data_pagamento)->format('d/m/Y') : '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <x-status-badge :status="$conta->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-4">
                    <a href="{{ route('financeiro.contas-pagar.edit', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                      <i class="bi bi-pencil-fill text-base"></i>
                    </a>
                    <form action="{{ route('financeiro.contas-pagar.destroy', $conta->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                        <i class="bi bi-trash-fill text-base"></i>
                      </button>
                    </form>
                    <button onclick="openAnexoModal({{ $conta->id }}, '{{ $conta->descricao }}')" class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                      <i class="bi bi-paperclip text-lg"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr id="details-{{ $conta->id }}" class="hidden details-row bg-gray-50 border-b border-gray-200">
                <td colspan="9" class="px-6 py-4">
                  <div class="flex flex-col md:flex-row gap-6 justify-between items-start">
                    <div class="flex-1 w-full">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3 flex items-center">
                        <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                      </h4>
                      @if ($conta->anexos && $conta->anexos->count() > 0)
                        <div class="flex flex-wrap gap-3">
                          @foreach ($conta->anexos as $anexo)
                            <div class="flex-1 min-w-[200px] bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                              <div class="flex items-center overflow-hidden min-w-0">
                                @if (Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
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
                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Ver">
                                  <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('anexos.download', $anexo->id) }}" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" title="Baixar">
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
                    <div class="flex flex-col gap-2 md:w-1/3">
                      <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full">
                        <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                          <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                            <i class="bi bi-clock-history mr-1"></i> Histórico Recente
                          </span>
                          <button type="button" onclick='openGeneralHistoryModal(@json($conta->history), @json($labelsContasPagar))' class="text-xs bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm">
                            Ver Completo
                          </button>
                        </div>

                        @if ($conta->history && $conta->history->count() > 0)
                          <div class="space-y-3">
                            @foreach ($conta->history->take(3) as $activity)
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

                        @if ($conta->last_user_id && (!$conta->history || $conta->history->count() == 0))
                          <div class="mt-2 pt-2 border-t border-blue-100 text-xs text-gray-500 text-center">
                            Última ação por: <strong>{{ $conta->editor->name ?? 'Sistema' }}</strong><br>
                            em {{ $conta->updated_at->format('d/m/Y H:i') }}
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nenhuma conta fixa encontrada.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div>
    <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
      Contas Avulsas
    </h2>

    <div class="bg-white p-8 rounded-lg shadow-md">
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"></th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">DANFE</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fornecedor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pagamento</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($contasVariaveis as $conta)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                  <button class="toggle-details-btn text-gray-500 hover:text-gray-800 transition-colors" data-target-id="{{ $conta->id }}">
                    <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                  </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $conta->danfe ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $conta->fornecedor ?? 'N/A' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $conta->descricao }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">R$ {{ number_format($conta->valor, 2, ',', '.') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $conta->data_vencimento ? \Carbon\Carbon::parse($conta->data_vencimento)->format('d/m/Y') : 'Não definida' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ $conta->data_pagamento ? \Carbon\Carbon::parse($conta->data_pagamento)->format('d/m/Y') : 'Não definida' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <x-status-badge :status="$conta->status" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-4">
                    <a href="{{ route('financeiro.contas-pagar.edit', $conta->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Editar">
                      <i class="bi bi-pencil-fill text-base"></i>
                    </a>
                    <form action="{{ route('financeiro.contas-pagar.destroy', $conta->id) }}" method="POST" onsubmit="return confirm('Tem certeza?');">
                      @csrf @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                        <i class="bi bi-trash-fill text-base"></i>
                      </button>
                    </form>
                    <button onclick="openAnexoModal({{ $conta->id }}, '{{ $conta->descricao }}')" class="text-gray-500 hover:text-blue-600 mr-3" title="Anexar Arquivo">
                      <i class="bi bi-paperclip text-lg"></i>
                    </button>
                  </div>
                </td>
              </tr>
              <tr id="details-{{ $conta->id }}" class="hidden details-row bg-gray-50 border-b border-gray-200">
                <td colspan="9" class="px-6 py-4">
                  <div class="flex flex-col gap-2">
                    <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                      <i class="bi bi-folder2-open mr-1"></i> Arquivos Anexados
                    </h4>
                    @if ($conta->anexos && $conta->anexos->count() > 0)
                      <div class="grid grid-cols-1 gap-3">
                        @foreach ($conta->anexos as $anexo)
                          <div class="bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition">
                            <div class="flex items-center overflow-hidden">
                              @if (Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
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
                              <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition" title="Ver">
                                <i class="bi bi-eye-fill"></i>
                              </a>
                              <a href="{{ route('anexos.download', $anexo->id) }}" class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded transition" title="Baixar">
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
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Nenhuma conta avulsa encontrada.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $contasVariaveis->appends(request()->query())->links() }}
    </div>
  </div>

  </div> <x-modal-history />
  <x-modal model-type="App\Models\ContasPagar" />

@endsection