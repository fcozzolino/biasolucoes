<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\CommentController;
use App\Models\Board;
use Illuminate\Http\Request;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {

  Route::get('/board/{id}', [BoardController::class, 'apiShow'])->name('api.board.show');

  // Cards
  Route::get('/cards/{card}', [CardController::class, 'show'])->name('api.cards.show');
  Route::post('/cards', [CardController::class, 'store'])->name('api.cards.store');
  Route::put('/cards/{card}', [CardController::class, 'update'])->name('api.cards.update');
  Route::post('/cards/update-order', [CardController::class, 'updateOrder'])->name('api.cards.updateOrder');
  Route::put('/cards/{card}/archive', [CardController::class, 'archive'])->name('api.cards.archive');
  Route::put('/cards/{card}/destroy', [CardController::class, 'softDelete'])->name('api.cards.softDelete');

  // Archive
  Route::get('/cards/archive', [CardController::class, 'archiveIndex'])->name('api.cards.archiveIndex');
  Route::post('/cards/{card}/attachments', [CardController::class, 'uploadAttachment'])->name('api.cards.attachments.upload');
  Route::delete('/attachments/{attachment}', [CardController::class, 'deleteAttachment'])->name('api.attachments.delete');
  Route::get('/attachments/{attachment}/download', [CardController::class, 'downloadAttachment'])->name('api.attachments.download');

  // Boards
  Route::post('/columns', [ColumnController::class, 'store'])->name('api.columns.store');
  Route::put('/columns/{column}', [ColumnController::class, 'update'])->name('api.columns.update');
  Route::put('/columns/{column}/update-color', [ColumnController::class, 'updateColor'])->name('api.columns.updateColor');
  Route::post('/columns/update-order', [ColumnController::class, 'updateOrder'])->name('api.columns.updateOrder');

  // Comments
  Route::get('/cards/{card}/comments', [CommentController::class, 'index']);
  Route::post('/cards/{card}/comments', [CommentController::class, 'store']);
  Route::put('/comments/{comment}', [CommentController::class, 'update']);
  Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);

  // Labels
  Route::get('/labels', [LabelController::class, 'index']);
    Route::post('/labels', [LabelController::class, 'store']);
    Route::get('/labels/{label}', [LabelController::class, 'show']);
    Route::put('/labels/{label}', [LabelController::class, 'update']);
    Route::delete('/labels/{label}', [LabelController::class, 'destroy']);

    // Etiquetas de um card específico
    Route::get('/cards/{card}/labels', [LabelController::class, 'cardLabels']);

    // Gerenciar etiquetas de um card
    Route::put('/cards/{card}/labels', [CardController::class, 'updateLabels']);
    Route::post('/cards/{card}/labels', [CardController::class, 'addLabel']);
    Route::delete('/cards/{card}/labels/{label}', [CardController::class, 'removeLabel']);

});
