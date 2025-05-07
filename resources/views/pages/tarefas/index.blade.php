@extends('layouts.horizontalLayout')

@section('title', 'Meus Quadros')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    Tarefas / <span class="text-muted fw-light">Quadros</span>
  </h4>

  <div class="row">
    @foreach ($boards as $board)
      <div class="col-md-4 mb-4">
        <a href="{{ route('board.show', $board->id) }}" class="text-decoration-none">
          <div class="card h-100">
            <div class="card-body">
              <h5 class="card-title">{{ $board->title }}</h5>
              <p class="card-text text-muted">Clique para abrir o Kanban</p>
            </div>
          </div>
        </a>
      </div>
    @endforeach
  </div>

  <!-- Formulário para criar novo Quadro -->
  <div class="row">
    <div class="col-md-6">
      <form action="{{ route('boards.store') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="title" class="form-label">Nome do Novo Quadro</label>
          <input type="text" name="title" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Criar Quadro</button>
      </form>
    </div>
  </div>

</div>
@endsection
