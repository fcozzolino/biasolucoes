<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\Card;

class CommentController extends Controller
{
    /**
     * Armazena um novo comentário para um card
     */
    public function store(Request $request, Card $card)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Usuário não autenticado'], 401);
        }

        $comment = $card->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return response()->json($comment->load('user'));
    }

    /**
     * Atualiza um comentário existente
     */
    public function update(Request $request, Comment $comment)
    {
        // Verificar se o usuário tem permissão para editar este comentário
        if (!auth()->check() || auth()->id() !== $comment->user_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $comment->update([
            'content' => $request->content
        ]);

        // Importante: carregue o relacionamento user ao retornar
        return response()->json($comment->load('user'));
    }

    /**
     * Remove um comentário
     */
    public function destroy(Comment $comment)
    {
        // Verificar se o usuário tem permissão para excluir este comentário
        if (!auth()->check() || auth()->id() !== $comment->user_id) {
            return response()->json(['error' => 'Não autorizado'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }
}
