<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class ClaimController extends Controller
{
    public function index()
    {
        return view('admin.claims');
    }
}
