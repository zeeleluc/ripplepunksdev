<?php

namespace App\Http\Controllers;

use App\Models\User;

class TestController extends Controller
{
    public function index()
    {
        $user = User::find(auth()->id());
        dd($user->xumm_token);

        return view('test');
    }
}
