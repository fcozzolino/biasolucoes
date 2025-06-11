<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BoardController extends Controller
{

  public function index()
  {
    $boards = Board::where('user_id', Auth::id())->get();
    return view('pages.tarefas.index', compact('boards'));
  }

  public function show($uuid)
  {
    $board = Board::where('uuid', $uuid)
      ->where('user_id', Auth::id())
      ->with(['columns.cards' => fn($q) => $q->with('labels')->orderBy('order')])
      ->first();

    if (!$board) {
      return redirect()->route('tarefas.index')
        ->with('error', 'Quadro não encontrado ou sem permissão de acesso');
    }

    $board->load(['columns.cards' => function ($q) {
      $q->whereNull('status')->orWhere('status', 0);
    }]);

    // Atualizar última visualização
    $board->update(['last_viewed_at' => now()]);

    return view('pages.tarefas.kanban-vue', ['boardUuid' => $uuid]);
  }

  public function store(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
    ]);

    Board::create([
      'title' => $request->title,
      'user_id' => Auth::id(),
    ]);

    return redirect()->route('tarefas.index')->with('success', 'Quadro criado com sucesso!');
  }

  public function apiShow($uuid)
  {
    $board = Board::where('uuid', $uuid)
      ->where('user_id', Auth::id())
      ->with([
        'columns.cards' => function ($query) {
          $query->with(['attachments', 'user', 'labels'])
            ->withCount('comments');
        }
      ])
      ->first();

    if (!$board) {
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    return response()->json($board);
  }

  // NOVOS MÉTODOS PARA A API

  public function apiIndex()
  {
    $boards = Board::where('user_id', Auth::id())
      ->with('user:id,name') // Carregar dados do usuário
      ->orderBy('last_viewed_at', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();

    return response()->json($boards);
  }

  public function apiStore(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'color' => 'required|string|max:7'
    ]);

    $board = Board::create([
      'title' => $request->title,
      'color' => $request->color,
      'user_id' => Auth::id(),
      'status' => 0
    ]);

    $board->load('user:id,name');

    return response()->json($board, 201);
  }

  public function apiUpdate(Request $request, $id)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'color' => 'required|string|max:7'
    ]);

    $board = Board::where('user_id', Auth::id())->findOrFail($id);

    $board->update([
      'title' => $request->title,
      'color' => $request->color
    ]);

    $board->load('user:id,name');

    return response()->json($board);
  }

  public function apiArchive(Board $board)
  {
    // Log adicional para debug
    Log::info('Request recebida:', [
      'url' => request()->url(),
      'route_parameters' => request()->route()->parameters(),
      'board_uuid_from_route' => request()->route('board'),
    ]);

    Log::info('Tentando arquivar board:', [
      'board_id' => $board->id,
      'board_user_id' => $board->user_id,
      'auth_user_id' => Auth::id(),
      'auth_check' => Auth::check(),
    ]);

    if ($board->user_id !== Auth::id()) {
      Log::error('Autorização falhou:', [
        'board_user_id' => $board->user_id,
        'auth_user_id' => Auth::id(),
      ]);
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    $board->update(['status' => 1]);
    return response()->json(['message' => 'Board arquivado com sucesso']);
  }

  public function apiDelete(Board $board)
  {
    Log::info('Tentando excluir board:', [
      'board_id' => $board->id,
      'board_user_id' => $board->user_id,
      'auth_user_id' => Auth::id(),
      'auth_check' => Auth::check(),
    ]);

    if ($board->user_id !== Auth::id()) {
      Log::error('Autorização falhou ao excluir:', [
        'board_user_id' => $board->user_id,
        'auth_user_id' => Auth::id(),
      ]);
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    $board->update(['status' => 5]);
    return response()->json(['message' => 'Board excluído com sucesso']);
  }


  public function apiUpdateLastViewed($id)
  {
    $board = Board::where('user_id', Auth::id())->findOrFail($id);
    $board->update(['last_viewed_at' => now()]);

    return response()->json(['message' => 'Last viewed updated']);
  }

  public function apiColumns($uuid)
  {
    $board = Board::where('uuid', $uuid)
      ->where('user_id', Auth::id())
      ->with(['columns.cards' => function ($query) {
        $query->with('labels')
                ->orderBy('order');
      }])
      ->first();

    if (!$board) {
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    return response()->json($board->columns);
  }

  public function archive(Board $board)
  {
    // Verifica se o usuário é o dono do board
    if ($board->user_id !== auth()->id()) {
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    $board->status = 'archived';
    $board->save();

    return response()->json(['message' => 'Board arquivado com sucesso']);
  }

  public function destroy(Board $board)
  {
    // Verifica se o usuário é o dono do board
    if ($board->user_id !== auth()->id()) {
      return response()->json(['error' => 'Não autorizado'], 403);
    }

    $board->delete();

    return response()->json(['message' => 'Board excluído com sucesso']);
  }
}
