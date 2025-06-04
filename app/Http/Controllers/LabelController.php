<?php
// app/Http/Controllers/LabelController.php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LabelController extends Controller
{
    /**
     * Display a listing of labels.
     */
    public function index(Request $request): JsonResponse
    {
        // Busca todas as etiquetas
        // Você pode filtrar por board_id ou user_id se necessário
        $labels = Label::query()
            ->when($request->board_id, function ($query, $boardId) {
                return $query->forBoard($boardId);
            })
            ->when($request->user_id, function ($query, $userId) {
                return $query->forUser($userId);
            })
            ->orderBy('name')
            ->get();

        return response()->json($labels);
    }

    /**
     * Store a newly created label.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'board_id' => 'nullable|exists:boards,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $label = Label::create([
            'name' => $request->name,
            'color' => $request->color,
            'board_id' => $request->board_id,
            'user_id' => Auth::id(), // Se quiser associar ao usuário
        ]);

        return response()->json($label, 201);
    }

    /**
     * Display the specified label.
     */
    public function show(Label $label): JsonResponse
    {
        return response()->json($label->load('cards'));
    }

    /**
     * Update the specified label.
     */
    public function update(Request $request, Label $label): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'color' => 'sometimes|required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $label->update($request->only(['name', 'color']));

        return response()->json($label);
    }

    /**
     * Remove the specified label.
     */
    public function destroy(Label $label): JsonResponse
    {
        // Remove a etiqueta de todos os cards primeiro (automático com onDelete cascade)
        $label->delete();

        return response()->json(['message' => 'Etiqueta removida com sucesso']);
    }

    /**
     * Get labels for a specific card
     */
    public function cardLabels($cardId): JsonResponse
    {
        $labels = Label::whereHas('cards', function ($query) use ($cardId) {
            $query->where('card_id', $cardId);
        })->get();

        return response()->json($labels);
    }
}
