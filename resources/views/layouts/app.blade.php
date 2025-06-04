<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Painel Principal')</title>

  <!-- Vite CSS/JS -->
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 min-h-screen">
    @php
        use Illuminate\Support\Facades\Auth;
    @endphp

  <!-- Header -->
  <header class="bg-white shadow-md fixed top-0 inset-x-0 z-50">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
      <div class="text-xl font-bold">Trello AI</div>
      <nav class="space-x-6">
        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-blue-600">Dashboard</a>
        <a href="#" class="text-gray-700 hover:text-blue-600">Tarefas</a>
        <a href="#" class="text-gray-700 hover:text-blue-600">Financeiro</a>
        <a href="#" class="text-gray-700 hover:text-blue-600">CRMs</a>
        <a href="#" class="text-gray-700 hover:text-blue-600">Configurações</a>
      </nav>
      <div class="flex items-center gap-4">
        <span class="text-gray-600">{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="text-red-500 hover:underline">Sair</button>
        </form>
      </div>
    </div>
    <script
  src="https://cdn.jsdelivr.net/npm/jkanban@1.3.1/dist/jkanban.min.js"
  defer
></script>
  </header>

  <!-- Conteúdo -->
  <main class="pt-24 px-6">
    @yield('content')
  </main>

</body>
</html>
