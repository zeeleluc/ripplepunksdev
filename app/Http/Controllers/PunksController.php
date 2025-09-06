<?php

namespace App\Http\Controllers;

class PunksController extends Controller
{
    public function index()
    {
        return view('punks');
    }

    public function show(int $id)
    {
        return view('punks.show', [
            'id' => $id,
        ]);
    }
}
