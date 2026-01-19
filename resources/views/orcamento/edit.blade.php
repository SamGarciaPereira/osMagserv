@extends('layouts.main')

@section('title', 'Magserv | Editar Orçamento')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Editar Orçamento</h1>
            <p class="text-gray-600 mt-1">Altere os dados da proposta Nº {{ $orcamento->numero_proposta }}.</p>
        </div>
        <a href="{{ route('orcamentos.index') }}"
            class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg">
            Voltar para a Lista
        </a>
    </div>

    <div class="bg-white p-8 rounded-lg shadow-md">
        <form action="{{ route('orcamentos.update', $orcamento->id) }}" method="POST">
            @csrf
            @method('PUT')

            @php
                $prefixoNumero = Str::beforeLast($orcamento->numero_proposta, '-');
                $sufixoNumero = Str::afterLast($orcamento->numero_proposta, '-');
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    <label for="cliente_id" class="block text-sm font-medium text-gray-700 mb-2">Cliente <span
                            class="text-red-500">*</span></label>
                    <select id="cliente_id" name="cliente_id"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="">Selecione um cliente</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ old('cliente_id', $orcamento->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nome }}
                            </option>
                        @endforeach
                    </select>
                    @error('cliente_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nº Proposta</label>
                    <div class="flex gap-2">
                        <div class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-600 font-mono font-bold">
                            <input type="text" value="{{ $prefixoNumero }}" readonly
                            class="w-full px-4 py-2 bg-gray-100 text-gray-600">
                        </div>

                        <input type="number" id="numero_proposta_sufixo" name="numero_proposta_sufixo"
                            value="{{ old('numero_proposta_sufixo', ltrim($sufixoNumero, '0')) }}"
                            class="w-28 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            min="0" max="999" required>
                    </div>
                    @error('numero_proposta_sufixo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>  

                <div>
                    <label for="data_solicitacao" class="block text-sm font-medium text-gray-700 mb-2">Data de Solicitação</label>
                    <input type="date" id="data_solicitacao" name="data_solicitacao"
                        value="{{ old('data_solicitacao', optional($orcamento->data_solicitacao)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('data_solicitacao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="data_envio" class="block text-sm font-medium text-gray-700 mb-2">Data de Envio</label>
                    <input type="date" id="data_envio" name="data_envio"
                        value="{{ old('data_envio', optional($orcamento->data_envio)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('data_envio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="data_aprovacao" class="block text-sm font-medium text-gray-700 mb-2">Data de
                        Aprovação</label>
                    <input type="date" id="data_aprovacao" name="data_aprovacao"
                        value="{{ old('data_aprovacao', optional($orcamento->data_aprovacao)->format('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('data_aprovacao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="valor" class="block text-sm font-medium text-gray-700 mb-2">Valor (R$)</label>
                    <input type="number" step="0.01" id="valor" name="valor" value="{{ old('valor', $orcamento->valor) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('valor') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>                

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span
                            class="text-red-500">*</span></label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                        required>
                        <option value="Pendente" {{ old('status', $orcamento->status) == 'Pendente' ? 'selected' : '' }}>
                            Pendente</option>
                        <option value="Em Andamento" {{ old('status', $orcamento->status) == 'Em Andamento' ? 'selected' : '' }}>Em Andamento</option>
                        <option value="Em Validação" {{ old('status', $orcamento->status) == 'Em Validação' ? 'selected' : '' }}>Em Validação</option>
                        <option value="Validado" {{ old('status', $orcamento->status) == 'Validado' ? 'selected' : '' }}>Validado</option>
                        <option value="Enviado" {{ old('status', $orcamento->status) == 'Enviado' ? 'selected' : '' }}>Enviado</option>
                        <option value="Aprovado" {{ old('status', $orcamento->status) == 'Aprovado' ? 'selected' : '' }}>Aprovado</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="revisao" class="block text-sm font-medium text-gray-700 mb-2">Revisão</label>
                    <input type="number" id="revisao" name="revisao" value="{{ old('revisao', $orcamento->revisao) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    @error('revisao') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="lg:col-span-3">
                    <label for="escopo" class="block text-sm font-medium text-gray-700 mb-2">Demanda</label>
                    <textarea id="escopo" name="escopo" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('escopo', $orcamento->escopo) }}</textarea>
                    @error('escopo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="lg:col-span-3">
                    <label for="comentario" class="block text-sm font-medium text-gray-700 mb-2">Comentários</label>
                    <textarea id="comentario" name="comentario" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">{{ old('comentario', $orcamento->comentario) }}</textarea>
                    @error('comentario') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                @if($orcamento->status == 'Pendente' || $orcamento->status == 'Em Andamento')
                <div class="lg:col-span-3">
                    <label for="checklist" class="block text-sm font-medium text-gray-700 mb-2">Checklist</label>
                    <div class="flex gap-2 mb-4">
                        <input type="text" id="new-task-input" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Adicionar nova tarefa...">
                        <button type="button" onclick="addTask()" 
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            <i class="bi bi-plus-lg"></i>
                        </button>
                    </div>
                    <ul id="checklist-container" class="space-y-2"></ul>
                    <input type="hidden" name="checklist_data" id="checklist_data" 
                        value="{{ json_encode($orcamento->checklist ?? []) }}">
                </div>
                @endif
            </div>

            <div class="flex justify-end mt-10 pt-6">
                <a href="{{ route('orcamentos.index') }}"
                    class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg mr-4">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg">
                    Atualizar Orçamento
                </button>
            </div>
        </form>
    </div>

@endsection