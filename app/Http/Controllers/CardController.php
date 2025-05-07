<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Attachment;
use Illuminate\Support\Facades\Auth;

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
        'user_id' => Auth::id(), // ✅ Aqui está o ponto-chave
    ]);

    return response()->json(['success' => true, 'card' => $card]);
}

    public function show(Card $card)
    {
        $card->load('user', 'attachments');

        return response()->json([
            'id' => $card->id,
            'title' => $card->title,
            'description' => $card->description,
            'full_description' => $card->full_description,
            'link' => $card->link,
            'color' => $card->color,
            'created_at' => $card->created_at->toDateTimeString(),
            'user' => [
                'name' => optional($card->user)->name ?? 'Desconhecido',
            ],
            'attachments' => $card->attachments->map(fn($att) => [
                'id' => $att->id,
                'filename' => $att->filename,
                'path' => $att->path,
                'mime_type' => $att->mime_type,
                'created_at' => $att->created_at->toDateTimeString(),
            ])
        ]);
    }



    public function update(Request $request, Card $card)
{
    $request->validate([
        'title' => 'sometimes|required|string|max:255',
        'description' => 'sometimes|nullable|string',
        'full_description' => 'sometimes|nullable|string',
        'link' => 'sometimes|nullable|url',
        'color' => 'sometimes|nullable|string|max:20',
        'status' => 'sometimes|nullable|in:1,5'
    ]);

    $card->update($request->only([
        'title', 'description', 'full_description',
        'link', 'color', 'status'
    ]));

    $card->load('attachments');

    return response()->json([
        'success' => true,
        'id' => $card->id,
        'title' => $card->title,
        'description' => $card->description,
        'color' => $card->color,
        'full_description' => $card->full_description,
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
                'path' => $attachment->path, // <- este campo precisa estar aqui
                'url' => Storage::disk('public')->url($path),
                'mime_type' => $attachment->mime_type,
                'created_at' => $attachment->created_at->toDateTimeString(),
            ]
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
    return response()->json(['success' => true]);
}

public function softDelete(Card $card)
{
    $card->update(['status' => 5]);
    return response()->json(['success' => true]);
}

}
