<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\Api\ActivityLogController;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

Route::post('/activity-log', [ActivityLogController::class, 'store'])->name('api.activity-log');


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
  return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
  // Boards - usando UUID
  Route::get('/boards', [BoardController::class, 'apiIndex'])->name('api.boards.index');
  Route::post('/boards', [BoardController::class, 'apiStore'])->name('api.boards.store');
  Route::get('/boards/{board}', [BoardController::class, 'apiShow'])->name('api.boards.show');
  Route::put('/boards/{board}', [BoardController::class, 'apiUpdate'])->name('api.boards.update');
  Route::patch('/boards/{board}/archive', [BoardController::class, 'apiArchive'])->name('api.boards.archive');
  Route::delete('/boards/{board}', [BoardController::class, 'apiDelete'])->name('api.boards.delete');
  Route::patch('/boards/{board}/view', [BoardController::class, 'apiUpdateLastViewed'])->name('api.boards.updateView');

  // Columns
  Route::get('/boards/{board}/columns', [BoardController::class, 'apiColumns'])->name('api.boards.columns');
  Route::post('/columns', [ColumnController::class, 'store'])->name('api.columns.store');
  Route::put('/columns/{column}', [ColumnController::class, 'update'])->name('api.columns.update');
  Route::put('/columns/{column}/update-color', [ColumnController::class, 'updateColor'])->name('api.columns.updateColor');
  Route::post('/columns/update-order', [ColumnController::class, 'updateOrder'])->name('api.columns.updateOrder');

  // Cards
  Route::get('/cards/{card}', [CardController::class, 'show'])->name('api.cards.show');
  Route::post('/cards', [CardController::class, 'store'])->name('api.cards.store');
  Route::put('/cards/{card}', [CardController::class, 'update'])->name('api.cards.update');
  Route::post('/cards/update-order', [CardController::class, 'updateOrder'])->name('api.cards.updateOrder');
  Route::put('/cards/{card}/archive', [CardController::class, 'archive'])->name('api.cards.archive');
  Route::delete('/cards/{card}', [CardController::class, 'softDelete'])->name('api.cards.softDelete');

  // Attachments
  Route::post('/cards/{card}/attachments', [CardController::class, 'uploadAttachment'])->name('api.cards.attachments.upload');
  Route::delete('/attachments/{attachment}', [CardController::class, 'deleteAttachment'])->name('api.attachments.delete');
  Route::get('/attachments/{attachment}/download', [CardController::class, 'downloadAttachment'])->name('api.attachments.download');

  // Comments
  Route::get('/cards/{card}/comments', [CommentController::class, 'index'])->name('api.comments.index');
  Route::post('/cards/{card}/comments', [CommentController::class, 'store'])->name('api.comments.store');
  Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('api.comments.update');
  Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('api.comments.destroy');

  // Labels
  Route::get('/labels', [LabelController::class, 'index'])->name('api.labels.index');
  Route::post('/labels', [LabelController::class, 'store'])->name('api.labels.store');
  Route::get('/labels/{label}', [LabelController::class, 'show'])->name('api.labels.show');
  Route::put('/labels/{label}', [LabelController::class, 'update'])->name('api.labels.update');
  Route::delete('/labels/{label}', [LabelController::class, 'destroy'])->name('api.labels.destroy');

  // Card Labels
  Route::get('/cards/{card}/labels', [LabelController::class, 'cardLabels'])->name('api.cards.labels');
  Route::put('/cards/{card}/labels', [CardController::class, 'updateLabels'])->name('api.cards.updateLabels');
  Route::post('/cards/{card}/labels', [CardController::class, 'addLabel'])->name('api.cards.addLabel');
  Route::delete('/cards/{card}/labels/{label}', [CardController::class, 'removeLabel'])->name('api.cards.removeLabel');


  // Rotas de Checklist
  Route::prefix('cards/{card}')->group(function () {
    Route::get('checklists', [ChecklistController::class, 'index']);
    Route::post('checklists', [ChecklistController::class, 'store']);
  });
  Route::prefix('checklists')->group(function () {
    Route::put('{checklist}', [ChecklistController::class, 'update']);
    Route::delete('{checklist}', [ChecklistController::class, 'destroy']);
    Route::post('{checklist}/items', [ChecklistController::class, 'addItem']);
    Route::put('{checklist}/reorder', [ChecklistController::class, 'reorderItems']);
  });
  Route::prefix('checklist-items')->group(function () {
    Route::put('{item}', [ChecklistController::class, 'updateItem']);
    Route::delete('{item}', [ChecklistController::class, 'destroyItem']);
  });
  Route::put('/checklists/{checklist}/reorder-items', [ChecklistController::class, 'reorderItems']);
});
