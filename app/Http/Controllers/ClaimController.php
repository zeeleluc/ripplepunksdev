<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class ClaimController extends Controller
{
    public function index()
    {
        return view('claim');
    }
}
