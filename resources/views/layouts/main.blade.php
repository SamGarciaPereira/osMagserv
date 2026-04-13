<!DOCTYPE html>
<html lang="pt-BR">
  
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Magserv')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">
    @vite('resources/css/app.css')
  </head>

  <body class="bg-gray-100 font-inter overflow-x-hidden min-h-screen">

    <header class="md:hidden fixed top-0 left-0 w-full h-16 bg-gray-900 flex items-center justify-between px-4 z-40 shadow-md">
      <img src="{{ asset('img/magservLogoBranco.png') }}" alt="Logo Magserv" class="h-8 object-contain">
      <button id="mobile-menu-btn" class="text-gray-300 hover:text-white focus:outline-none">
        <i class="bi bi-list text-3xl"></i>
      </button>
    </header>

    <div id="sidebar-backdrop" class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity md:hidden"></div>

    <div id="sidebar" class="fixed top-0 left-0 h-screen bg-gray-900 text-white p-4 flex flex-col z-50 w-64 md:w-20 transform -translate-x-full md:translate-x-0 transition-all duration-300 rounded-r-lg md:rounded-tr-lg md:rounded-br-lg overflow-y-auto md:overflow-visible">

      <div id="sidebar-header" class="flex items-center justify-between md:justify-center pb-4 border-b border-gray-700 h-16 transition-all duration-300">
        <div id="sidebar-branding" class="flex items-center min-w-0 overflow-hidden transition-all duration-300 w-auto opacity-100 md:w-0 md:opacity-0">
          <img id="sidebar-logo" src="{{ asset('img/magservLogoBranco.png') }}" alt="Logo Magserv" class="h-10 ml-3 object-contain transition-all duration-300">
        </div>
        <button id="mobile-close-btn" class="md:hidden text-gray-400 hover:text-white focus:outline-none flex-shrink-0">
          <i class="bi bi-x-lg text-2xl"></i>
        </button>
        <button id="sidebar-toggle" class="hidden md:block text-gray-400 hover:text-white focus:outline-none flex-shrink-0">
          <i class="bi bi-list text-2xl"></i>
        </button>
      </div>

      <nav class="flex-grow mt-4 space-y-2">
        <a href="{{ route('home') }}" class="p-2.5 pl-3.5 flex items-center rounded-md hover:bg-blue-600 group">
          <i class="bi bi-house-door-fill text-lg min-w-[1.5rem]"></i>
          <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Menu</span>
        </a>
        <a href="{{ route('processos.index') }}" class="p-2.5 pl-3.5 flex items-center rounded-md hover:bg-blue-600 group">
          <i class="bi bi-inboxes-fill text-lg min-w-[1.5rem]"></i>
          <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Processos</span>
        </a>
        @if (!auth()->user()->isSupervisor())
          <a href="{{ route('orcamentos.index') }}" class="p-2.5 pl-3.5 flex items-center rounded-md hover:bg-blue-600 group">
            <i class="bi bi-file-earmark-ruled-fill text-lg min-w-[1.5rem]"></i>
            <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Orçamentos</span>
          </a>
        @endif
        @if (!auth()->user()->isSupervisor())
          <a href="{{ route('clientes.index') }}" class="p-2.5 pl-3.5 flex items-center rounded-md hover:bg-blue-600 group">
            <i class="bi bi-people-fill text-lg min-w-[1.5rem]"></i>
            <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Clientes</span>
          </a>
        @endif
        <div>
          <button id="dropdown-btn-manutencao" class="w-full p-2.5 pl-3.5 flex items-center justify-between rounded-md hover:bg-blue-600 group">
            <div class="flex items-center">
              <i class="bi bi-hammer text-lg min-w-[1.5rem]"></i>
              <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Manutenção</span>
            </div>
            <i class="sidebar-text bi bi-chevron-down text-xs w-auto opacity-100 md:w-0 md:opacity-0 transition-all" id="arrow-manutencao"></i>
          </button>
          <div id="submenu-manutencao" class="hidden flex-col mt-1 pl-10">
            @if (!auth()->user()->isSupervisor())
              <a href="{{ route('contratos.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Contratos</a>
            @endif
            <a href="{{ route('manutencoes.corretiva.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Manutenções Corretivas</a>
            <a href="{{ route('manutencoes.preventiva.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Manutenções Preventivas</a>
            <a href="{{ route('manutencoes.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Relatórios</a>
          </div>
        </div>
        @if (auth()->user()->isAdmin())
          <div>
            <button id="dropdown-btn-financeiro" class="w-full p-2.5 pl-3.5 flex items-center justify-between rounded-md hover:bg-blue-600 group">
              <div class="flex items-center">
                <i class="bi bi-piggy-bank-fill text-lg min-w-[1.5rem]"></i>
                <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Financeiro</span>
              </div>
              <i class="sidebar-text bi bi-chevron-down text-xs w-auto opacity-100 md:w-0 md:opacity-0 transition-all" id="arrow-financeiro"></i>
            </button>
            <div id="submenu-financeiro" class="hidden flex-col mt-1 pl-10">
              <a href="{{ route('financeiro.contas-pagar.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Contas a pagar</a>
              <a href="{{ route('financeiro.contas-receber.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Contas a receber</a>
            </div>
          </div>
          <div>
            <button id="dropdown-btn-rh" class="w-full p-2.5 pl-3.5 flex items-center justify-between rounded-md hover:bg-blue-600 group">
              <div class="flex items-center">
                <i class="bi bi-person-fill-gear text-lg min-w-[1.5rem]"></i>
                <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">RH</span>
              </div>
              <i class="sidebar-text bi bi-chevron-down text-xs w-auto opacity-100 md:w-0 md:opacity-0 transition-all" id="arrow-rh"></i>
            </button>
            <div id="submenu-rh" class="hidden flex-col mt-1 pl-10">
              <a href="{{ route('rh.funcionarios.index') }}" class="sidebar-text text-sm text-gray-300 p-1 rounded-md hover:bg-gray-700 w-full opacity-100 md:opacity-0 transition-all">Funcionários</a>
            </div>
          </div>
        @endif
        @if (!auth()->user()->isSupervisor())
          <a href="{{ route('admin.solicitacao.index') }}" class="p-2.5 pl-3.5 flex items-center rounded-md hover:bg-blue-600 group">
            <i class="bi bi-chat-dots-fill text-lg min-w-[1.5rem]"></i>
            <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Solicitações</span>
          </a>
        @endif
      </nav>

      <div class="mt-auto">
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" class="p-2.5 pl-3.5 text-left flex items-center rounded-md hover:bg-blue-600 group w-full">
            <i class="bi bi-box-arrow-in-right text-lg min-w-[1.5rem]"></i>
            <span class="sidebar-text text-sm font-semibold ml-4 w-auto opacity-100 md:w-0 md:opacity-0 whitespace-nowrap transition-all">Logout</span>
          </button>
        </form>
      </div>
    </div>

    <main id="main-content" class="min-h-screen pt-16 md:pt-0 md:ml-20 overflow-x-hidden flex flex-col justify-between bg-gray-100 transition-all duration-300">
      <div class="p-4 sm:p-6 lg:p-8 flex-grow w-full max-w-[1920px] mx-auto">
        @yield('content')
      </div>

      <footer class="text-center p-4 text-gray-500 text-sm">
        <p>Magserv Manutenção e Serviços LTDA &copy; {{ date('Y') }} | Samuel Software Developer</p>
      </footer>
    </main>
    @vite('resources/js/app.js')
  </body>

</html>