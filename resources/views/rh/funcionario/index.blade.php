@extends('layouts.main')

@section('title', 'Magserv | Funcionários')

@section('content')

    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Lista de Funcionários</h1>
            <p class="text-gray-600 mt-1">Gerencie todos os funcionários cadastrados no sistema.</p>
        </div>
        <a href="{{ route('rh.funcionarios.create') }}"
            class="bg-blue-600 text-white hover:bg-blue-700 font-medium py-2 px-4 rounded-lg flex items-center shadow-sm">
            <i class="bi bi-plus-lg mr-2"></i>
            Cadastrar Novo Funcionário
        </a>
    </div>

@endsection