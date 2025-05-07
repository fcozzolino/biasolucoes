<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BoardController extends Controller
{
    public function index()
    {
        $boards = Board::where('user_id', Auth::id())->get();
        return view('pages.tarefas.index', compact('boards'));
    }

    public function show($id)
    {
        $board = Board::with(['columns.cards' => fn($q) => $q->orderBy('order')])
            ->where('user_id', Auth::id())
            ->findOrFail($id);
        $board->load(['columns.cards' => function ($q) {
            $q->whereNull('status')->orWhere('status', 0);
        }]);

        return view('pages.tarefas.kanban', compact('board'));
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
}
