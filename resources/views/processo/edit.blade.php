@extends('layouts.main')

@section('title', 'Masgerv | Editar Processo')

@section('content')

  <div class="p-4 sm:p-6 lg:p-8">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
      <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Editar Processo</h1>
        <p class="text-gray-600 mt-1 text-sm sm:text-base">Altere os dados do processo vinculado à proposta Nº {{ $processo->orcamento->numero_proposta }}.</p>
      </div>
      <a href="{{ route('processos.index') }}" class="w-full sm:w-auto text-center bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg transition-colors">
        Voltar para a Lista
      </a>
    </div>

    @if ($errors->any())
      <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
        <p class="font-bold">Atenção!</p>
        <ul class="list-disc pl-5 mt-1">
          @foreach ($errors->all() as $error)
            <li class="text-sm">{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="bg-white p-4 sm:p-6 lg:p-8 rounded-lg shadow-md relative">
      <form action="{{ route('processos.update', $processo->id) }}" method="POST" id="form-processo" data-valor-orcamento="{{ $processo->orcamento->valor ?? 0 }}">
        @csrf
        @method('PUT')

        <h2 class="text-lg sm:text-xl font-semibold text-gray-800 pb-2 sm:pb-4 mb-4 sm:mb-6 border-b border-gray-100">Status do Processo</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1 sm:mb-2">Status <span class="text-red-500">*</span></label>
            <div class="flex gap-2">
              <select id="status" name="status" data-original="{{ $processo->status }}" class="w-full px-3 py-2 sm:px-4 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
                <option value="">Selecione um status</option>

                <option value="Em Aberto" {{ old('status', $processo->status) == 'Em Aberto' ? 'selected' : '' }}>
                  Em Aberto
                </option>

                <option value="Finalizado" {{ old('status', $processo->status) == 'Finalizado' ? 'selected' : '' }}>
                  Finalizado
                </option>

                @if (!auth()->user()->isSupervisor())
                  <option value="Faturado" {{ old('status', $processo->status) == 'Faturado' ? 'selected' : '' }}>
                    Faturado
                  </option>
                @endif
              </select>
              @if (!auth()->user()->isSupervisor())
                <button type="button" id="btn-open-modal" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors flex-shrink-0" title="Gerenciar Parcelas">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 sm:w-6 sm:h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                  </svg>
                </button>
              @endif
            </div>
            @if (!auth()->user()->isSupervisor())
              <p class="text-xs text-gray-500 mt-1.5" id="resumo-parcelas">
                <i class="bi bi-info-circle mr-1"></i> {{ $processo->contasReceber->count() }} parcela(s) definida(s).
              </p>
            @endif

            @error('status')
              <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>
        @if (!auth()->user()->isSupervisor())
          <div id="faturamentoModal" class="fixed inset-0 z-[9999] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-900/50 transition-opacity backdrop-blur-sm" onclick="closeFaturamentoModal()"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
              <div class="flex min-h-full items-center justify-center p-4 text-center">
                <div class="relative w-full transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:max-w-4xl flex flex-col">

                  <div class="bg-white p-4 lg:p-6 border-b border-gray-100 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                      <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 sm:h-12 sm:w-12 rounded-full bg-blue-100">
                        <i class="bi bi-cash-stack text-blue-600 text-lg sm:text-xl"></i>
                      </div>
                      <div>
                        <h3 class="text-lg sm:text-xl font-semibold leading-6 text-gray-900" id="modal-title">
                          Gerenciar Faturamento
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-1">
                          Orçamento de: <span class="font-bold text-gray-800">R$ {{ number_format($processo->orcamento->valor, 2, ',', '.') }}</span>
                        </p>
                      </div>
                    </div>
                    <button type="button" onclick="closeFaturamentoModal()" class="text-gray-400 hover:text-gray-700 bg-gray-50 hover:bg-gray-100 border border-transparent hover:border-gray-200 rounded-full p-2 transition-all flex-shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500" title="Fechar">
                      <i class="bi bi-x-lg text-sm sm:text-base"></i>
                    </button>
                  </div>

                  <div class="bg-gray-50 p-4 lg:p-6 flex-1 text-left overflow-y-auto max-h-[60vh]">
                    <div class="border border-gray-200 rounded-md overflow-x-auto bg-white shadow-sm">
                      <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                          <tr>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">NF</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[200px]">Descrição</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[120px]">Valor (R$)</th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[140px]">Vencimento</th>
                            <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                          </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="container-parcelas">
                          @foreach ($processo->contasReceber as $index => $conta)
                            <tr class="hover:bg-gray-50">
                              <input type="hidden" name="parcelas[{{ $index }}][id]" value="{{ $conta->id }}">
                              <td class="px-3 py-2">
                                <input type="text" name="parcelas[{{ $index }}][nf]" value="{{ $conta->nf }}" class="block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                              </td>
                              <td class="px-3 py-2">
                                <input type="text" name="parcelas[{{ $index }}][descricao]" value="{{ $conta->descricao }}" class="block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                              </td>
                              <td class="px-3 py-2">
                                <input type="number" step="0.01" name="parcelas[{{ $index }}][valor]" value="{{ $conta->valor }}" class="parcela-valor block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                              </td>
                              <td class="px-3 py-2">
                                <input type="date" name="parcelas[{{ $index }}][data_vencimento]" value="{{ $conta->data_vencimento?->format('Y-m-d') }}" class="block w-full text-sm border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                              </td>
                              <td class="px-3 py-2 text-center">
                                <button type="button" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded transition-colors btn-remove-parcela" title="Remover Parcela">
                                  <i class="bi bi-trash"></i>
                                </button>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>

                    <div class="mt-4 text-left">
                      <button type="button" id="btn-add-parcela" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded transition-colors">
                        <i class="bi bi-plus-circle mr-1.5"></i> Adicionar Parcela
                      </button>
                    </div>
                  </div>

                  <div class="bg-white px-4 py-4 lg:px-6 border-t border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-700 font-medium order-2 sm:order-1 bg-gray-50 px-3 py-2 rounded-lg border border-gray-200 w-full sm:w-auto text-center sm:text-left">
                      Total: R$ <span id="total-parcelas" class="font-bold text-gray-900">0,00</span>
                      <span id="msg-validacao" class="ml-1 block sm:inline text-xs mt-1 sm:mt-0"></span>
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row w-full sm:w-auto gap-3 order-1 sm:order-2">
                      <button type="button" onclick="closeFaturamentoModal()" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-2.5 sm:py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Cancelar
                      </button>
                      <button type="button" id="btn-confirm-modal" class="w-full sm:w-auto inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-2.5 sm:py-2 bg-blue-600 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                        Confirmar Parcelas
                      </button>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        @endif

        <div class="flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 mt-8 sm:mt-10 pt-4 sm:pt-6 border-t border-gray-100">
          <a href="{{ route('processos.index') }}" class="w-full sm:w-auto text-center bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg transition-colors">
            Cancelar
          </a>
          <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg transition-colors shadow-sm">
            Salvar Alterações
          </button>
        </div>
      </form>
    </div>
  </div>

  @vite('resources/js/app.js')

@endsection
