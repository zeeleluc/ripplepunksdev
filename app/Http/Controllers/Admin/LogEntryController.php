<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogEntry;
use Illuminate\Support\Facades\Auth;

class LogEntryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        abort_unless($user && $user->isAdmin(), 403);

        // Fetch recent log entries, for example 20 latest, ordered by created_at descending
        $logEntries = LogEntry::orderBy('created_at', 'desc')->take(20)->get();

        return view('admin.log-entry', [
            'logEntries' => $logEntries,
        ]);
    }
}
