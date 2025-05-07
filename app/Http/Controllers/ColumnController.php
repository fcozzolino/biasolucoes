<?php

namespace App\Http\Controllers;

use App\Models\Column;
use Illuminate\Http\Request;

class ColumnController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'board_id' => 'required|exists:boards,id',
      'name' => 'required|string|max:255',
    ]);

    $column = Column::create([
      'board_id' => $request->board_id,
      'name' => $request->name,
      'order' => Column::where('board_id', $request->board_id)->max('order') + 1,
    ]);

    return response()->json([
      'success' => true,
      'column' => $column,
    ]);
  }


  public function updateOrder(Request $request)
  {
    $columns = $request->columns;

    foreach ($columns as $column) {
      \App\Models\Column::where('id', $column['id'])->update([
        'order' => $column['order']
      ]);
    }

    return response()->json(['status' => 'ok']);
  }

  // ColumnController.php
  public function updateColor(Request $request, Column $column)
  {
    $request->validate([
      'color' => 'required|string|max:20'
    ]);

    $column->color = $request->color;
    $column->save();

    return response()->json(['success' => true]);
  }

  public function update(Request $request, Column $column)
  {
    $request->validate(['name' => 'required|string|max:255']);
    $column->update(['name' => $request->name]);
    return response()->json(['success' => true]);
  }
}