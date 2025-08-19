<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users');
    }
}
