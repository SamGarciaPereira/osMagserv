@extends('layouts.main')

@section('title', 'Magserv | Dashboard')

@section('content')

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 mt-1">
                Bem-vindo, <strong>{{ auth()->user()->name ?? 'Usuário' }}</strong>! 
                Dados de: 
                <span class="font-semibold bg-blue-50 text-blue-800 px-2 py-0.5 rounded border border-blue-100 capitalize">
                    {{ \Carbon\Carbon::parse($inputInicio)->locale('pt_BR')->translatedFormat('F/Y') }}
                </span>
                @if($inputFim)
                até <span class="font-semibold bg-blue-50 text-blue-800 px-2 py-0.5 rounded border border-blue-100 capitalize">
                    {{ \Carbon\Carbon::parse($inputFim)->locale('pt_BR')->translatedFormat('F/Y') }}
                </span>
                @endif
            </p>
        </div>
        
        <form method="GET" action="{{ route('home') }}" class="bg-white p-3 rounded-lg shadow-sm border border-gray-200">
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="data_inicio" class="text-sm text-gray-600 font-medium">De:</label>
                    <input 
                        type="month" 
                        name="data_inicio" 
                        id="data_inicio" 
                        value="{{ $inputInicio }}"
                        required
                        class="border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <span class="hidden sm:inline text-gray-300">|</span>

                <div class="flex items-center gap-2">
                    <label for="data_fim" class="text-sm text-gray-600 font-medium">Até:</label>
                    <input 
                        type="month" 
                        name="data_fim" 
                        id="data_fim" 
                        value="{{ $inputFim }}"
                        class="border-gray-300 rounded-md text-sm py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                <button type="submit" class="w-full sm:w-auto bg-blue-600 text-white px-4 py-1.5 rounded-md text-sm hover:bg-blue-700 transition flex items-center justify-center gap-2 shadow-sm">
                    <i class="bi bi-filter"></i> <span class="sm:hidden">Filtrar</span>
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-gray-600 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500  tracking-wide">Processos</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($processosStats) }}</h3>
                </div>
                <div class="bg-gray-100 text-gray-600 p-2 rounded-lg">
                    <i class="bi bi-inboxes-fill text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$processosStats" />
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-yellow-500 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500  tracking-wide">Orçamentos</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($orcamentosStats) }}</h3>
                </div>
                <div class="bg-yellow-50 text-yellow-600 p-2 rounded-lg">
                    <i class="bi bi-file-earmark-text-fill text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$orcamentosStats" />
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-black-500 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500 tracking-wide">Contratos</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($contratosStats) }}</h3>
                </div>
                <div class="bg-indigo-50 text-black-600 p-2 rounded-lg">
                    <i class="bi bi-file-earmark-check-fill text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$contratosStats" />
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-blue-500 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500  tracking-wide">Manut. Preventiva</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($prevStats) }}</h3>
                </div>
                <div class="bg-blue-50 text-blue-600 p-2 rounded-lg">
                    <i class="bi bi-shield-check text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$prevStats" />
            </div>
        </div>
        
        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-orange-500 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500 tracking-wide">Manut. Corretiva</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($corrStats) }}</h3>
                </div>
                <div class="bg-orange-50 text-orange-600 p-2 rounded-lg">
                    <i class="bi bi-tools text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$corrStats" />
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-purple-500 hover:shadow-lg transition">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <p class="text-md font-bold text-gray-500  tracking-wide">Solicitações</p>
                    <h3 class="text-3xl font-bold text-gray-800">{{ array_sum($solicitacoesStats) }}</h3>
                </div>
                <div class="bg-purple-50 text-purple-600 p-2 rounded-lg">
                    <i class="bi bi-chat-dots-fill text-xl"></i>
                </div>
            </div>
            <div class="border-t border-gray-100 pt-2">
                <x-dashboard-list :stats="$solicitacoesStats" />
            </div>
        </div>
        
        
        @if(auth()->user()->isAdmin())
            <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-green-500 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-md font-bold text-gray-500  tracking-wide">Receitas</p>
                        <h3 class="text-2xl font-bold text-gray-800">R$ {{ number_format($receberStats['Pago'] ?? 0, 2, ',', '.') }}</h3>
                    </div>
                    <div class="bg-green-50 text-green-600 p-2 rounded-lg">
                        <i class="bi bi-arrow-down-circle-fill text-xl"></i>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-2">
                    <x-dashboard-list :stats="$receberStats" :isMoney="true" />
                </div>
            </div>

            <div class="bg-white p-4 rounded-lg shadow-md border-t-4 border-red-500 hover:shadow-lg transition">
                <div class="flex justify-between items-start mb-2">
                    <div>
                        <p class="text-md font-bold text-gray-500  tracking-wide">Despesas</p>
                        <h3 class="text-2xl font-bold text-gray-800">R$ {{ number_format($pagarStats['Pago'] ?? 0, 2, ',', '.') }}</h3>
                    </div>
                    <div class="bg-red-50 text-red-600 p-2 rounded-lg">
                        <i class="bi bi-arrow-up-circle-fill text-xl"></i>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-2">
                    <x-dashboard-list :stats="$pagarStats" :isMoney="true" />
                </div>
            </div>
        @endif
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        @if(auth()->user()->isAdmin())
            <div class="lg:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800" id="chartTitle">Fluxo Financeiro (Receitas)</h3>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            Ref: {{ \Carbon\Carbon::parse($inputInicio)->locale('pt_BR')->translatedFormat('F/Y') }}
                            @if($inputFim)
                            até {{ \Carbon\Carbon::parse($inputFim)->locale('pt_BR')->translatedFormat('F/Y') }}
                            @endif
                        </span>
                    </div>
                    
                    <div class="flex bg-gray-100 p-1 rounded-lg">
                        <button onclick="updateChart('receita')" id="btnReceita" 
                                class="px-4 py-1.5 text-sm font-medium rounded-md shadow-sm bg-white text-blue-600 transition-all duration-200">
                            Receitas
                        </button>
                        <button onclick="updateChart('despesa')" id="btnDespesa" 
                                class="px-4 py-1.5 text-sm font-medium rounded-md text-gray-500 hover:text-gray-700 transition-all duration-200">
                            Despesas
                        </button>
                    </div>
                </div>
                
                <div class="relative h-80 w-full">
                    <canvas id="financeChart"></canvas>
                </div>
            </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module">
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof window.initDashboard === 'function') {
                window.initDashboard(
                    @json($labelsGrafico),
                    @json($dadosReceita),
                    @json($dadosDespesa)
                );
            }
        });
    </script>

@endsection