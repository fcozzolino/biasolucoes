@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 p-6">

    <!-- Card 1 -->
    <div class="bg-white rounded-lg shadow p-5">
      <div class="text-sm text-gray-500">Tarefas Abertas</div>
      <div class="text-3xl font-bold text-blue-600 mt-1">12</div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-lg shadow p-5">
      <div class="text-sm text-gray-500">Tarefas Concluídas</div>
      <div class="text-3xl font-bold text-green-600 mt-1">28</div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-lg shadow p-5">
      <div class="text-sm text-gray-500">Financeiro Pendente</div>
      <div class="text-3xl font-bold text-yellow-600 mt-1">R$ 1.280</div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-lg shadow p-5">
      <div class="text-sm text-gray-500">Novos Leads</div>
      <div class="text-3xl font-bold text-purple-600 mt-1">9</div>
    </div>

  </div>
@endsection
