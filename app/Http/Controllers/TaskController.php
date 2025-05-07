<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Exibe a tela principal do Kanban (Trello AI)
     */
    public function index()
    {
        return view('tasks.index');
    }
}
