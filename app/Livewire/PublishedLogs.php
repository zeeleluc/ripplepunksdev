<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\LogEntry;

class PublishedLogs extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind'; // or 'bootstrap' depending on your frontend

    public function render()
    {
        $logs = LogEntry::where('is_published', true)
            ->latest()
            ->paginate(10);

        return view('livewire.published-logs', [
            'logs' => $logs,
        ]);
    }
}
