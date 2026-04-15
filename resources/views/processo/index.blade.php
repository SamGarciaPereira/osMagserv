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

  <div class="p-2 sm:p-4 lg:p-6">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Gestão de Processos</h1>
        <p class="text-gray-600 mt-1 text-sm sm:text-base">Visualize e gerencie todos os processos em andamento.</p>
      </div>
    </div>

    <div class="bg-white p-4 sm:p-6 rounded-lg shadow-sm mb-6 border border-gray-200">
      <form method="GET" action="{{ route('processos.index') }}" class="grid grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">

        <div class="col-span-2 lg:col-span-4">
          <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
          <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <i class="bi bi-search text-gray-400"></i>
            </div>
            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Nº Proposta, Cliente ou Demanda..." class="w-full pl-10 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
          </div>
        </div>

        <div class="col-span-1 lg:col-span-2">
          <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="">Todos</option>
            <option value="Em Aberto" {{ request('status') == 'Em Aberto' ? 'selected' : '' }}>Em Aberto</option>
            <option value="Finalizado" {{ request('status') == 'Finalizado' ? 'selected' : '' }}>Finalizado</option>
            @if (!auth()->user()->isSupervisor())
              <option value="Faturado" {{ request('status') == 'Faturado' ? 'selected' : '' }}>Faturado</option>
            @endif
          </select>
        </div>

        <div class="col-span-1 lg:col-span-2">
          <label for="ordem" class="block text-sm font-medium text-gray-700 mb-1">Ordenar</label>
          <select name="ordem" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
            <option value="recentes" {{ request('ordem') == 'recentes' ? 'selected' : '' }}>Recentes</option>
            <option value="antigos" {{ request('ordem') == 'antigos' ? 'selected' : '' }}>Antigos</option>
            @if (!auth()->user()->isSupervisor())
              <option value="maior_valor" {{ request('ordem') == 'maior_valor' ? 'selected' : '' }}>Maior Valor</option>
              <option value="menor_valor" {{ request('ordem') == 'menor_valor' ? 'selected' : '' }}>Menor Valor</option>
            @endif
          </select>
        </div>

        <div class="col-span-1 lg:col-span-2">
          <label for="data_inicio" class="block text-sm font-medium text-gray-700 mb-1">De</label>
          <input type="month" name="data_inicio" id="data_inicio" value="{{ request('data_inicio') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="col-span-1 lg:col-span-1">
          <label for="data_fim" class="block text-sm font-medium text-gray-700 mb-1">Até</label>
          <input type="month" name="data_fim" id="data_fim" value="{{ request('data_fim') }}" class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-blue-500 focus:border-blue-500">
        </div>

        <div class="col-span-2 lg:col-span-1 mt-2 lg:mt-0">
          <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded-md text-sm hover:bg-blue-700 transition flex items-center justify-center shadow-sm" title="Filtrar">
            <i class="bi bi-filter text-base"></i> <span class="ml-2 lg:hidden">Aplicar Filtros</span>
          </button>
        </div>

      </form>
    </div>

    @if (session('success'))
      <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
        <p>{{ session('success') }}</p>
      </div>
    @endif

    <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-md">
      <div class="overflow-x-auto">
        <table class="w-full table-auto">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-10"></th>
              @if (!auth()->user()->isSupervisor())
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NF</th>
              @endif
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Proposta</th>
              <th class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cliente</th>
              @if (!auth()->user()->isSupervisor())
                <th class="hidden sm:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor</th>
              @endif
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            @forelse ($processos as $processo)
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-4 py-4">
                  <button class="toggle-details-btn text-gray-500 hover:text-blue-600 p-1" data-target-id="{{ $processo->id }}">
                    <i class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                  </button>
                </td>
                @if (!auth()->user()->isSupervisor())
                  <td class="px-4 py-4 align-middle">
                    @if ($processo->nf)
                      @php
                        $notas = array_filter(array_map('trim', explode(',', $processo->nf)));
                      @endphp

                      <div class="grid grid-cols-2 xl:grid-cols-3 gap-2 min-w-[100px] xl:min-w-[140px]">
                        @foreach ($notas as $nota)
                          <span class="bg-white text-blue-700 px-1 py-1 rounded text-xs font-bold border border-blue-200 shadow-sm whitespace-nowrap text-center truncate" title="{{ $nota }}">
                            {{ $nota }}
                          </span>
                        @endforeach
                      </div>
                    @else
                      <span class="text-gray-400 italic text-xs">N/A</span>
                    @endif
                  </td>
                @endif
                <td class="px-4 py-4 whitespace-nowrap">
                  @if ($processo->orcamento->numero_proposta)
                    <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded text-xs font-mono font-bold border border-gray-300 select-all">
                      {{ $processo->orcamento->numero_proposta }}
                    </span>
                  @else
                    <span class="text-xs text-gray-400 italic">N/A</span>
                  @endif
                </td>
                <td class="max-w-[226px] truncate hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-700" title="{{ $processo->orcamento->cliente->nome ?? 'N/A' }}">
                  {{ $processo->orcamento->cliente->nome ?? 'N/A' }}
                </td>
                @if (!auth()->user()->isSupervisor())
                  <td class="hidden sm:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-700 font-medium">R$ {{ number_format($processo->orcamento->valor ?? 0, 2, ',', '.') }}</td>
                @endif
                <td class="px-4 py-4 whitespace-nowrap">
                  @php
                    $statusLabel = $processo->status;
                    $statusClass = 'bg-gray-100 text-gray-800 border border-gray-200';
                    $detalheFinanceiro = '';

                    if ($processo->status == 'Faturado') {
                        $totalParcelas = $processo->contasReceber->sum('valor');
                        $valorOrcamento = $processo->orcamento->valor;

                        if (abs($totalParcelas - $valorOrcamento) > 0.01) {
                            $statusLabel = 'Faturado (Parcial)';
                            $statusClass = 'bg-orange-50 text-orange-700 border border-orange-200';
                            $porcentagem = $valorOrcamento > 0 ? ($totalParcelas / $valorOrcamento) * 100 : 0;
                            $detalheFinanceiro = number_format($porcentagem, 0) . '% (' . number_format($totalParcelas, 2, ',', '.') . ')';
                        } else {
                            $statusLabel = 'Faturado (Total)';
                            $statusClass = 'bg-green-50 text-green-700 border border-green-200';
                        }
                    } elseif ($processo->status == 'Em Aberto') {
                        $statusClass = 'bg-yellow-50 text-yellow-700 border border-yellow-200';
                    } elseif ($processo->status == 'Finalizado') {
                        $statusClass = 'bg-blue-50 text-blue-700 border border-blue-200';
                    }
                  @endphp

                  <div class="flex flex-col items-start">
                    <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }}">
                      {{ $statusLabel }}
                    </span>
                    @if (!auth()->user()->isSupervisor())
                      @if ($detalheFinanceiro)
                        <span class="text-[10px] text-gray-500 mt-1 ml-1" title="Valor Faturado">
                          {{ $detalheFinanceiro }}
                        </span>
                      @endif
                    @endif
                  </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex items-center space-x-3">
                    <a href="{{ route('processos.edit', $processo->id) }}" class="text-indigo-600 hover:text-indigo-900 p-1" title="Gerenciar Processo">
                      <i class="bi bi-pencil-fill text-base"></i>
                    </a>
                    <button onclick="openAnexoModal({{ $processo->id }})" class="text-gray-500 hover:text-blue-600 p-1" title="Anexar Arquivo">
                      <i class="bi bi-paperclip text-lg"></i>
                    </button>
                  </div>
                </td>
              </tr>

              <tr id="details-{{ $processo->id }}" class="hidden details-row bg-gray-50">
                <td colspan="7" class="px-4 sm:px-6 py-4">
                  <div class="text-sm text-gray-700 bg-white border border-gray-200 rounded p-3 mb-4 shadow-sm w-full overflow-hidden">
                    <p class="m-0">
                      <strong><i class="bi bi-card-text mr-1 text-gray-400"></i> Demanda:</strong><br>
                      <span class="mt-1 block break-words whitespace-normal text-justify">{{ $processo->orcamento->escopo ?: 'Não definido' }}</span>
                    </p>
                  </div>

                  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start w-full">

                    @php
                      $eSupervisor = auth()->user()->isSupervisor();

                      $anexosProcesso = ($processo->anexos ?? collect())->when($eSupervisor, function ($query) {
                          return $query->where('is_confidencial', false);
                      });

                      $anexosOrcamento = ($processo->orcamento ? $processo->orcamento->anexos : collect())->when($eSupervisor, function ($query) {
                          return $query->where('is_confidencial', false);
                      });
                    @endphp

                    <div class="flex flex-col gap-2 min-w-0 w-full">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                        <i class="bi bi-folder2-open mr-1"></i> Anexos do Processo
                      </h4>

                      @if ($anexosProcesso->count() > 0)
                        <div class="grid gap-3 grid-cols-1">
                          @foreach ($anexosProcesso as $anexo)
                            <div class="w-full bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition min-w-0 overflow-hidden">
                              <div class="flex items-center gap-3 min-w-0 flex-1">
                                @if (Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
                                  <i class="bi bi-file-earmark-pdf-fill text-red-500 text-2xl flex-shrink-0"></i>
                                @else
                                  <i class="bi bi-file-earmark-image-fill text-blue-500 text-2xl flex-shrink-0"></i>
                                @endif
                                <div class="min-w-0 flex-1">
                                  <p class="text-sm font-medium text-gray-700 truncate" title="{{ $anexo->nome_original }}">
                                    {{ $anexo->nome_original }}
                                  </p>
                                  <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                              </div>
                              <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 rounded transition">
                                  <i class="bi bi-eye-fill"></i>
                                </a>
                                @if (!$eSupervisor)
                                  <form action="{{ route('anexos.destroy', $anexo->id) }}" method="POST" onsubmit="return confirm('Excluir arquivo?');" class="inline m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-500 hover:text-red-600 rounded transition">
                                      <i class="bi bi-trash"></i>
                                    </button>
                                  </form>
                                @endif
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @else
                        <p class="text-sm text-gray-500 italic bg-white p-3 rounded border border-dashed border-gray-200 text-center">Sem anexos.</p>
                      @endif
                    </div>

                    <div class="flex flex-col gap-2 min-w-0 w-full">
                      <h4 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 flex items-center">
                        <i class="bi bi-paperclip mr-1"></i> Anexos do Orçamento
                      </h4>

                      @if ($anexosOrcamento->count() > 0)
                        <div class="grid gap-3 grid-cols-1">
                          @foreach ($anexosOrcamento as $anexo)
                            <div class="w-full bg-white border border-gray-200 rounded-md p-3 flex items-center justify-between shadow-sm hover:shadow-md transition min-w-0 overflow-hidden">
                              <div class="flex items-center gap-3 min-w-0 flex-1">
                                @if (Str::endsWith(strtolower($anexo->nome_original), '.pdf'))
                                  <i class="bi bi-file-earmark-pdf-fill text-red-500 text-xl flex-shrink-0"></i>
                                @else
                                  <i class="bi bi-file-earmark-image-fill text-blue-500 text-xl flex-shrink-0"></i>
                                @endif
                                <div class="min-w-0 flex-1">
                                  <p class="text-sm font-medium text-gray-700 truncate" title="{{ $anexo->nome_original }}">
                                    {{ $anexo->nome_original }}
                                  </p>
                                  <p class="text-xs text-gray-400">{{ $anexo->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                              </div>
                              <div class="flex items-center gap-1 ml-2 flex-shrink-0">
                                <a href="{{ route('anexos.show', ['anexo' => $anexo->id, 'filename' => $anexo->nome_original]) }}" target="_blank" class="p-1.5 text-gray-500 hover:text-blue-600 rounded transition">
                                  <i class="bi bi-eye-fill"></i>
                                </a>
                              </div>
                            </div>
                          @endforeach
                        </div>
                      @else
                        <p class="text-sm text-gray-500 italic bg-white p-3 rounded border border-dashed border-gray-200 text-center">Sem anexos.</p>
                      @endif
                    </div>

                    <div class="flex flex-col gap-2 min-w-0 w-full">
                      <div class="bg-blue-50 border border-blue-200 rounded-md p-3 h-full overflow-hidden">
                        <div class="flex justify-between items-center mb-3 border-b border-blue-200 pb-2">
                          <span class="text-xs font-bold text-blue-800 uppercase flex items-center">
                            <i class="bi bi-clock-history mr-1"></i> Histórico
                          </span>
                          <button type="button" onclick='openGeneralHistoryModal(@json($processo->history), @json($labelsProcesso))' class="text-[10px] bg-white border border-blue-300 text-blue-700 hover:bg-blue-600 hover:text-white px-2 py-1 rounded transition shadow-sm flex-shrink-0">
                            Ver Tudo
                          </button>
                        </div>

                        @if ($processo->history && $processo->history->count() > 0)
                          <div class="space-y-3">
                            @foreach ($processo->history->take(3) as $activity)
                              <div class="flex flex-col text-xs text-gray-600 bg-white bg-opacity-60 p-1.5 rounded border border-blue-100 min-w-0">
                                <div class="flex justify-between font-semibold text-gray-700">
                                  <span class="text-blue-900 truncate pr-1">
                                    {{ $activity->version }}ª Edição
                                  </span>
                                  <span class="text-gray-500 text-[9px] flex-shrink-0">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m H:i') }}
                                  </span>
                                </div>
                                <div class="mt-0.5 truncate italic">
                                  Por: {{ $activity->user->name ?? 'Sistema' }}
                                </div>
                              </div>
                            @endforeach
                          </div>
                        @else
                          <p class="text-xs italic text-gray-400 text-center py-4">Sem histórico.</p>
                        @endif
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                  <div class="flex flex-col items-center justify-center">
                    <i class="bi bi-inbox text-3xl mb-2 text-gray-300"></i>
                    <p>Nenhum processo encontrado com estes filtros.</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <x-modal-history />
  <x-modal model-type="App\Models\Processo" />

@endsection
