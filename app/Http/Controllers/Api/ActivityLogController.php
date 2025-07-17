<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function store(Request $request)
    {
        ActivityLog::log(
            $request->input('event', 'custom_event'),
            $request->input('description', 'Descrição genérica'),
            $request->input('properties', []),
            $request->input('type', 'auth')
        );

        return response()->json(['message' => 'Log registrado com sucesso']);
    }
}
