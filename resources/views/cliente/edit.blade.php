@extends('layouts.main')

@section('title', 'Magserv | Editar Cliente')

@section('content')

<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Editar Cliente</h1>
        <p class="text-gray-600 mt-1">Altere os dados do cliente "{{ $cliente->nome }}".</p>
    </div>
    <a href="{{ route('clientes.index') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-4 rounded-lg">
        Voltar para a Lista
    </a>
</div>

<div class="bg-white p-8 rounded-lg shadow-md">
    <form action="{{ route('clientes.update', $cliente->id) }}" method="POST">
        @csrf
        @method('PUT') 
        
        <h2 class="text-xl font-semibold text-gray-800 pb-4 mb-6">Informações Pessoais</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome Completo / Razão Social <span class="text-red-500">*</span></label>
                <input type="text" id="nome" name="nome" value="{{ $cliente->nome }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label for="documento" class="block text-sm font-medium text-gray-700 mb-2">CPF / CNPJ <span class="text-red-500">*</span></label>
                <input type="text" id="documento" name="documento" value="{{ $cliente->documento }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div>
                <label for="responsavel" class="block text-sm font-medium text-gray-700 mb-2">Responsável</label>
                <input type="text" id="responsavel" name="responsavel" value="{{ $cliente->responsavel }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">E-mail</label>
                <input type="email" id="email" name="email" value="{{ $cliente->email }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                <input type="tel" id="telefone" name="telefone" value="{{ $cliente->telefone }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="matriz_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Vincular a uma Matriz (Opcional)
                </label>
                <select name="matriz_id" id="matriz_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Este cliente é uma Matriz / Unidade Única</option>
                        @foreach($clientes as $c)
                            @if(!isset($cliente) || $cliente->id !== $c->id)
                                <option value="{{ $c->id }}" 
                                    {{ (old('matriz_id', $cliente->matriz_id ?? '') == $c->id) ? 'selected' : '' }}>
                                    {{ $c->nome }}
                                </option>
                            @endif
                        @endforeach
                </select>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-gray-800 pb-4 mt-10 mb-6">Endereço</h2>
        <div class="grid grid-cols-1 md:grid-cols-6 gap-6">
            <div class="md:col-span-2">
                <label for="cep" class="block text-sm font-medium text-gray-700 mb-2">CEP</label>
                <div class="flex gap-2">
                    <input type="text" id="cep" name="cep" 
                        value="{{ isset($cliente) ? $cliente->cep : '' }}" 
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Ex: 01001-000">
                    
                    <button type="button" id="btn-buscar-cep" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors" title="Buscar CEP">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
            <div class="md:col-span-4">
                <label for="logradouro" class="block text-sm font-medium text-gray-700 mb-2">Logradouro</label>
                <input type="text" id="logradouro" name="logradouro" value="{{ $cliente->logradouro }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">Número</label>
                <input type="text" id="numero" name="numero" value="{{ $cliente->numero }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-4">
                <label for="bairro" class="block text-sm font-medium text-gray-700 mb-2">Bairro</label>
                <input type="text" id="bairro" name="bairro" value="{{ $cliente->bairro }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-3">
                <label for="cidade" class="block text-sm font-medium text-gray-700 mb-2">Cidade</label>
                <input type="text" id="cidade" name="cidade" value="{{ $cliente->cidade }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-3">
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <input type="text" id="estado" name="estado" value="{{ $cliente->estado }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="flex justify-end mt-10 pt-6">
            <a href="{{ route('clientes.index') }}" class="bg-gray-200 text-gray-700 hover:bg-gray-300 font-medium py-2 px-6 rounded-lg mr-4">
                Cancelar
            </a>
            <button type="submit" class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-6 rounded-lg">
                Atualizar Cliente
            </button>
        </div>
    </form>
</div>

@endsection
