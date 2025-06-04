<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CardController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'column_id' => 'required|exists:columns,id',
      'title' => 'required|string|max:255',
      'description' => 'nullable|string',
    ]);

    $order = Card::where('column_id', $request->column_id)->max('order') ?? -1;

    $card = Card::create([
      'column_id' => $request->column_id,
      'title' => $request->title,
      'description' => $request->description,
      'order' => $order + 1,
      'user_id' => Auth::id(),
    ]);

    return response()->json([
      'success' => true,
      'card' => [
        'id' => $card->id,
        'title' => $card->title,
        'description' => $card->description,
        'color' => $card->color,
        'created_at' => $card->created_at->timestamp,
        'full_description' => $card->full_description,
        'attachments_count' => $card->attachments()->count(),
      ]
    ]);
  }


  public function show(Card $card)
  {
    $card->load(['user', 'attachments', 'comments.user']);

    return response()->json([
      'id' => $card->id,
      'title' => $card->title,
      'description' => $card->description,
      'full_description' => $card->full_description,
      'link' => $card->link,
      'color' => $card->color,
      'start_date' => $card->start_date,
      'due_date' => $card->due_date,
      'reminder_interval' => $card->reminder_interval,
      'created_at' => $card->created_at->toDateTimeString(),
      'user' => [
        'name' => optional($card->user)->name ?? 'Desconhecido',
      ],
      'attachments' => $card->attachments->map(function ($att) {
        return [
          'id' => $att->id,
          'filename' => $att->filename,
          'path' => $att->path,
          'mime_type' => $att->mime_type,
          'created_at' => $att->created_at->toDateTimeString(),
        ];
      }),
      'comments' => $card->comments->map(function ($cmt) {
        return [
          'id' => $cmt->id,
          'content' => $cmt->content,
          'created_at' => $cmt->created_at->toDateTimeString(),
          'user' => [
            'id' => $cmt->user->id,
            'name' => $cmt->user->name,
          ]
        ];
      }),
      'comments_count' => $card->comments()->count(),
    ]);

    $card = Card::with([
            'column',
            'attachments',
            'comments.user',
            'labels' // ADICIONE esta linha
        ])->findOrFail($id);

        return response()->json($card);
  }






  public function update(Request $request, Card $card)
  {
    $request->validate([
      'title' => 'sometimes|required|string|max:255',
      'description' => 'sometimes|nullable|string',
      'full_description' => 'sometimes|nullable|string',
      'link' => 'sometimes|nullable|url',
      'color' => 'sometimes|nullable|string|max:20',
      'status' => 'sometimes|nullable|in:1,5',
      'start_date' => 'nullable|date',
      'due_date' => 'nullable|date',
      'reminder_interval' => 'nullable|string|max:50'
    ]);

    $card->update($request->only([
      'title',
      'description',
      'full_description',
      'link',
      'color',
      'status',
      'start_date',
      'due_date',
      'reminder_interval',
    ]));

    $card->load('attachments');

    return response()->json([
      'success' => true,
      'id' => $card->id,
      'title' => $card->title,
      'description' => $card->description,
      'color' => $card->color,
      'full_description' => $card->full_description,
      'start_date' => $card->start_date,
      'due_date' => $card->due_date,
      'reminder_interval' => $card->reminder_interval,
      'attachments' => $card->attachments,
    ]);
  }




  public function updateOrder(Request $request)
  {
    $cardIds = $request->cards;
    $columnId = $request->column_id;

    foreach ($cardIds as $index => $cardId) {
      Card::where('id', $cardId)->update([
        'order' => $index,
        'column_id' => $columnId
      ]);
    }

    return response()->json(['status' => 'success']);
  }

  public function uploadAttachment(Request $request, Card $card)
  {
    Log::info('UPLOAD INICIADO', $request->all());

    $request->validate([
      'file' => 'required|file|max:10240', // 10MB
    ]);

    $request->validate([
      'file' => 'required|file|max:10240', // 10MB
    ]);

    $file = $request->file('file');
    $path = $file->store("attachments/{$card->id}", 'public');

    $attachment = $card->attachments()->create([
      'filename' => $file->getClientOriginalName(),
      'path' => $path,
      'mime_type' => $file->getMimeType(),
    ]);

    return response()->json([
      'success' => true,
      'attachment' => [
        'id' => $attachment->id,
        'filename' => $attachment->filename,
        'path' => $attachment->path,
        'mime_type' => $attachment->mime_type,
        'created_at' => $attachment->created_at->toDateTimeString(),
      ],
      'card' => $card, // ← importante para atualização JS
    ]);
  }

  public function deleteAttachment(Attachment $attachment)
  {
    // Verifica se o arquivo existe antes de tentar deletar
    if (Storage::disk('public')->exists($attachment->path)) {
      Storage::disk('public')->delete($attachment->path);
    }

    // Deleta o registro do banco de dados
    $attachment->delete();

    return response()->json(['success' => true]);
  }

  public function downloadAttachment(Attachment $attachment)
  {
    return Storage::disk('public')->download($attachment->path, $attachment->filename);
  }


  public function archive(Card $card)
  {
    $card->update(['status' => 1]);
    return response()->json($card->fresh()); // IMPORTANTE usar fresh() para garantir dados atualizados
  }


  public function softDelete(Card $card)
  {
    $card->update(['status' => 5]);
    return response()->json($card);
  }


  public function updateLabels(Request $request, Card $card): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label_ids' => 'required|array',
            'label_ids.*' => 'exists:labels,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        // Sincroniza as etiquetas (remove antigas e adiciona novas)
        $card->syncLabels($request->label_ids);

        // Recarrega o card com as etiquetas atualizadas
        $card->load('labels');

        return response()->json([
            'message' => 'Etiquetas atualizadas com sucesso',
            'labels' => $card->labels
        ]);
    }


    public function addLabel(Request $request, Card $card): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'label_id' => 'required|exists:labels,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Dados inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $card->attachLabel($request->label_id);
        $card->load('labels');

        return response()->json([
            'message' => 'Etiqueta adicionada com sucesso',
            'labels' => $card->labels
        ]);
    }

     public function removeLabel(Card $card, $labelId): JsonResponse
    {
        $card->detachLabel($labelId);
        $card->load('labels');

        return response()->json([
            'message' => 'Etiqueta removida com sucesso',
            'labels' => $card->labels
        ]);
    }
}
