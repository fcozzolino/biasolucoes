{{-- resources/views/pages/kanban.blade.php --}}
@extends('layouts.contentNavbarLayout')

@section('title', 'Kanban - Tarefas')

@section('content')
  <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
      <span class="text-muted fw-light">Tarefas /</span> Kanban
    </h4>

    <!-- Kanban Vuexy Style -->
    <div class="row">
      <div class="col-lg-4 mb-4">
        <div class="bg-body p-3 rounded shadow-sm">
          <h5 class="fw-semibold mb-3">A Fazer</h5>
          <div class="kanban-column">
            <div class="bg-light p-3 rounded mb-2">Tarefa 1</div>
            <div class="bg-light p-3 rounded mb-2">Tarefa 2</div>
            <div class="bg-light p-3 rounded mb-2">Tarefa 3</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="bg-body p-3 rounded shadow-sm">
          <h5 class="fw-semibold mb-3">Em Progresso</h5>
          <div class="kanban-column">
            <div class="bg-light p-3 rounded mb-2">Tarefa 4</div>
            <div class="bg-light p-3 rounded mb-2">Tarefa 5</div>
          </div>
        </div>
      </div>
      <div class="col-lg-4 mb-4">
        <div class="bg-body p-3 rounded shadow-sm">
          <h5 class="fw-semibold mb-3">Concluído</h5>
          <div class="kanban-column">
            <div class="bg-light p-3 rounded mb-2">Tarefa 6</div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

