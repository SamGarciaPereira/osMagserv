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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cargo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo de Contrato</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($funcionarios as $funcionario)
                        <tr>
                            <td class="px-6 py-4">
                                <button class="toggle-details-btn text-gray-500 hover:text-gray-800"
                                    data-target-id="{{ $funcionario->id }}">
                                    <i
                                        class="bi bi-chevron-down toggle-arrow inline-block transition-transform duration-300"></i>
                                </button>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $funcionario->nome }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $funcionario->telefone ?? 'Não informado' }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $funcionario->cargo ?? 'Não informado' }}</div>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $funcionario->tipo_contrato ?? 'Não informado' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-4">
                                    <a href="{{ route('rh.funcionarios.edit', $funcionario->id) }}"
                                        class="text-indigo-600 hover:text-indigo-900" title="Editar">
                                        <i class="bi bi-pencil-fill text-base"></i>
                                    </a>
                                    <form action="{{ route('rh.funcionarios.destroy', $funcionario->id) }}" method="POST"
                                        onsubmit="return confirm('Tem certeza que deseja remover este funcionário?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Remover">
                                            <i class="bi bi-trash-fill text-base"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <tr id="details-{{ $funcionario->id }}" class="hidden details-row">
                            <td colspan="6" class="px-6 py-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p><strong>Data de Nascimento:</strong>
                                            {{ $funcionario->data_nascimento ? $funcionario->data_nascimento->format('d/m/Y') : 'Não informado' }}
                                        </p>
                                        <p><strong>CPF:</strong> {{ $funcionario->cpf ?? 'Não informado' }}</p>
                                        <p><strong>RG:</strong> {{ $funcionario->rg ?? 'Não informado' }}</p>
                                    </div>
                                    <div>
                                        <p><strong>Endereço:</strong> {{ $funcionario->endereco ?? 'Não informado' }}</p>
                                        <p><strong>E-mail:</strong> {{ $funcionario->email ?? 'Não informado' }}</p>
                                        <p><strong>Data de Admissão:</strong>
                                            {{ $funcionario->data_admissao ? $funcionario->data_admissao->format('d/m/Y') : 'Não informado' }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Nenhum funcionário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
        
    </div> <x-modal-history />
    </div> <x-modal model-type="App\Models\Funcionario" />
@endsection