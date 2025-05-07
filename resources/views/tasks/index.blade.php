@extends('layouts.app')

@section('title', 'Kanban')

@section('content')
<div class="app-kanban overflow-hidden">
  <!-- Kanban Wrapper -->
  <div class="kanban-wrapper bg-light p-4 rounded-2xl">

    <!-- Board 1 -->
    <div class="kanban-board bg-white shadow rounded-2xl p-4 mr-4 w-72 flex-shrink-0">
      <div class="kanban-board-header flex justify-between items-center mb-3">
        <h6 class="font-semibold">A Fazer</h6>
        <div class="text-sm text-gray-400">3</div>
      </div>
      <div class="kanban-board-body space-y-4">
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 1</div>
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 2</div>
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 3</div>
      </div>
    </div>

    <!-- Board 2 -->
    <div class="kanban-board bg-white shadow rounded-2xl p-4 mr-4 w-72 flex-shrink-0">
      <div class="kanban-board-header flex justify-between items-center mb-3">
        <h6 class="font-semibold">Em Progresso</h6>
        <div class="text-sm text-gray-400">2</div>
      </div>
      <div class="kanban-board-body space-y-4">
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 4</div>
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 5</div>
      </div>
    </div>

    <!-- Board 3 -->
    <div class="kanban-board bg-white shadow rounded-2xl p-4 mr-4 w-72 flex-shrink-0">
      <div class="kanban-board-header flex justify-between items-center mb-3">
        <h6 class="font-semibold">Concluído</h6>
        <div class="text-sm text-gray-400">1</div>
      </div>
      <div class="kanban-board-body space-y-4">
        <div class="kanban-item bg-gray-100 p-3 rounded-lg">Tarefa 6</div>
      </div>
    </div>

  </div>
</div>
@endsection
