<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
//use App\Http\Controllers\TaskController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\ColumnController;
use App\Http\Controllers\CardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



Route::post('/upload-teste', function (Request $request) {
  if ($request->hasFile('file')) {
    $file = $request->file('file');
    $path = $file->store('attachments/teste', 'public');
    return response()->json([
      'success' => true,
      'path' => $path,
      'url' => Storage::disk('public')->url($path),
    ]);
  }
  return response()->json(['success' => false, 'message' => 'Nenhum arquivo enviado.']);
});


Route::get('/', function () {
  return view('welcome');
});

Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/teste', function () {
  return view('teste');
});

//Route::get('/tasks', [TaskController::class, 'index'])->middleware(['auth'])->name('tasks.index');

Route::get('/kanban', function () {
  return view('pages.kanban');
})->middleware(['auth'])->name('kanban');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  Route::get('/tarefas', [BoardController::class, 'index'])->name('tarefas.index');
  //Route::get('/board/{id}', [BoardController::class, 'show'])->name('board.show');

  Route::get('/board/{id}', function ($id) {
    return view('vue-kanban', ['boardId' => $id]);
  })->middleware(['auth'])->name('board.vue');




  Route::post('/columns', [ColumnController::class, 'store'])->name('columns.store');
  Route::post('/cards', [CardController::class, 'store'])->name('cards.store');
  Route::post('/cards/update-order', [CardController::class, 'updateOrder'])->name('cards.updateOrder');
  Route::post('/boards', [BoardController::class, 'store'])->name('boards.store');
  Route::post('/columns/update-order', [ColumnController::class, 'updateOrder'])->name('columns.updateOrder');
  Route::get('/cards/{card}', [CardController::class, 'show'])->name('cards.show');
  Route::put('/cards/{card}', [CardController::class, 'update'])->name('cards.update');
  Route::post('/cards/{card}/attachments', [CardController::class, 'uploadAttachment'])->name('cards.attachments.upload');
  Route::delete('/attachments/{attachment}', [CardController::class, 'deleteAttachment'])->name('attachments.delete');
  Route::get('/attachments/{attachment}/download', [CardController::class, 'downloadAttachment'])->name('attachments.download');
  Route::put('/cards/{card}/archive', [CardController::class, 'archive'])->name('cards.archive');
  Route::delete('/cards/{card}/destroy', [CardController::class, 'softDelete'])->name('cards.softDelete');
  Route::put('/columns/{column}/update-color', [ColumnController::class, 'updateColor'])->name('columns.updateColor');
  Route::put('/columns/{column}', [ColumnController::class, 'update'])->name('columns.update');




  Route::post('/upload-teste', function (Request $request) {
    if ($request->hasFile('file')) {
      $file = $request->file('file');
      $path = $file->store('attachments/teste', 'public');
      return response()->json([
        'success' => true,
        'path' => $path,
        'url' => Storage::disk('public')->url($path),
      ]);
    }
    return response()->json(['success' => false, 'message' => 'Nenhum arquivo enviado.']);
  });
  Route::get('/upload-teste', function () {
    return view('upload-teste');
  });
});

require __DIR__ . '/auth.php';
