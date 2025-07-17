<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Checklist;
use App\Models\ChecklistItem;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChecklistController extends Controller
{
  /**
   * Criar um novo checklist
   */
  public function store(Request $request, Card $card): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'title' => 'required|string|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    $position = $card->checklists()->max('position') ?? -1;

    $checklist = $card->checklists()->create([
      'title' => $request->title,
      'position' => $position + 1
    ]);

    return response()->json($checklist->load('items'), 201);
  }

  /**
   * Atualizar checklist
   */
  public function update(Request $request, Checklist $checklist): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'title' => 'sometimes|required|string|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    $checklist->update($request->only(['title']));

    return response()->json($checklist->load('items'));
  }

  /**
   * Deletar checklist
   */
  public function destroy(Checklist $checklist): JsonResponse
  {
    $checklist->delete();
    return response()->json(['message' => 'Checklist removido com sucesso']);
  }

  /**
   * Adicionar item ao checklist
   */
  public function addItem(Request $request, Checklist $checklist): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'content' => 'required|string|max:255'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    $position = $checklist->items()->max('position') ?? -1;

    $item = $checklist->items()->create([
      'content' => $request->content,
      'position' => $position + 1
    ]);

    return response()->json([
      'item' => $item,
      'checklist' => $checklist->fresh(['items'])
    ], 201);
  }

  /**
   * Atualizar item do checklist
   */
  public function updateItem(Request $request, ChecklistItem $item): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'content' => 'sometimes|required|string|max:255',
      'is_completed' => 'sometimes|boolean'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    if ($request->has('is_completed')) {
      $item->update([
        'is_completed' => $request->is_completed,
        'completed_at' => $request->is_completed ? now() : null
      ]);
    }

    if ($request->has('content')) {
      $item->update(['content' => $request->content]);
    }

    return response()->json([
      'item' => $item,
      'checklist' => $item->checklist->fresh(['items'])
    ]);
  }

  /**
   * Deletar item do checklist
   */
  public function destroyItem(ChecklistItem $item): JsonResponse
  {
    $checklist = $item->checklist;
    $item->delete();

    return response()->json([
      'message' => 'Item removido com sucesso',
      'checklist' => $checklist->fresh(['items'])
    ]);
  }

  /**
   * Reordenar itens
   */
  public function reorderItems(Request $request, Checklist $checklist): JsonResponse
  {
    $validator = Validator::make($request->all(), [
      'items' => 'required|array',
      'items.*.id' => 'required|exists:checklist_items,id',
      'items.*.position' => 'required|integer|min:0'
    ]);

    if ($validator->fails()) {
      return response()->json([
        'message' => 'Dados inválidos',
        'errors' => $validator->errors()
      ], 422);
    }

    foreach ($request->items as $itemData) {
      ChecklistItem::where('id', $itemData['id'])
        ->where('checklist_id', $checklist->id)
        ->update(['position' => $itemData['position']]);
    }

    return response()->json([
      'message' => 'Itens reordenados com sucesso',
      'checklist' => $checklist->fresh(['items'])
    ]);
  }

  public function index(Card $card)
  {
    $checklists = $card->checklists()
      ->with('items')
      ->orderBy('created_at', 'asc')
      ->get();

    return response()->json($checklists);
  }

  
}
