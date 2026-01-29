@extends('layouts.main')

@section('title', 'Masgerv | Editar Processo')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Processo</h1>
            <p class="text-gray-600 mt-1">Altere os dados do processo vinculado à proposta Nº {{ $processo->orcamento->numero_proposta }}.</p>
        </div>
        <a href="{{ route('processos.index') }}"
           class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg">
            Voltar para a Lista
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Atenção!</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white p-8 rounded-lg shadow-md relative">
        <form action="{{ route('processos.update', $processo->id) }}" 
              method="POST" 
              id="form-processo"
              data-valor-orcamento="{{ $processo->orcamento->valor ?? 0 }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select id="status" name="status"
                                data-original="{{ $processo->status }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <option value="">Selecione um status</option>
                            @foreach(['Em Aberto', 'Finalizado', 'Faturado'] as $st)
                                <option value="{{ $st }}" {{ old('status', $processo->status) == $st ? 'selected' : '' }}>
                                    {{ $st }}
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="button" id="btn-open-modal" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200" title="Gerenciar Parcelas">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </button>
                    </div>
                    
                    <p class="text-xs text-gray-500 mt-1" id="resumo-parcelas">
                        {{ $processo->contasReceber->count() }} parcela(s) definida(s).
                    </p>
                    
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div id="faturamentoModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="fixed inset-0 bg-opacity-10 backdrop-blur-sm transition-opacity"></div>

                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                        
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="bi bi-cash-stack text-blue-600 text-xl"></i>
                                </div>

                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Gerenciar Faturamento
                                    </h3>
                                    
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 mb-6">
                                            Defina as parcelas para o orçamento de: 
                                            <span class="font-bold text-gray-800">R$ {{ number_format($processo->orcamento->valor, 2, ',', '.') }}</span>
                                        </p>

                                        <div class="border rounded-md overflow-hidden">
                                            <table class="min-w-full divide-y divide-gray-200">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NF</th>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descrição</th>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Valor (R$)</th>
                                                        <th scope="col" class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vencimento</th>
                                                        <th scope="col" class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Ação</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white divide-y divide-gray-200" id="container-parcelas">
                                                    @foreach($processo->contasReceber as $index => $conta)
                                                    <tr>
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
                                                            <button type="button" class="text-red-500 hover:text-red-700 btn-remove-parcela">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-3 text-left">
                                            <button type="button" id="btn-add-parcela" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                                                <i class="bi bi-plus-circle mr-1"></i> Adicionar Parcela
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse justify-between items-center">
                            <div class="sm:flex sm:flex-row-reverse">
                                <button type="button" id="btn-confirm-modal" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                    Confirmar
                                </button>
                                <button type="button" onclick="closeFaturamentoModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Cancelar
                                </button>
                            </div>
                            
                            <div class="text-sm text-gray-700 mt-3 sm:mt-0 font-medium">
                                Total: R$ <span id="total-parcelas">0,00</span>
                                <span id="msg-validacao" class="ml-1"></span>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-10 pt-6">
                <a href="{{ route('processos.index') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg mr-4">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    @vite('resources/js/app.js')

@endsection