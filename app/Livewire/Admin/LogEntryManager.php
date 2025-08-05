<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\LogEntry;

class LogEntryManager extends Component
{
    public $logs;
    public $text;
    public $link;
    public $is_published = true;
    public $editingLogId = null;

    public $confirmingDeletionId = null;

    protected $rules = [
        'text' => 'required|string',
        'link' => 'nullable|url',
        'is_published' => 'boolean',
    ];

    public function mount()
    {
        $this->loadLogs();
    }

    public function loadLogs()
    {
        $this->logs = LogEntry::orderBy('created_at', 'desc')->get();
    }

    public function createLog()
    {
        $this->validate();

        LogEntry::create([
            'text' => $this->text,
            'link' => $this->link,
            'is_published' => $this->is_published,
        ]);

        $this->resetInput();
        $this->loadLogs();
        session()->flash('message', 'Log entry created!');
    }

    public function editLog($id)
    {
        $log = LogEntry::findOrFail($id);
        $this->editingLogId = $log->id;
        $this->text = $log->text;
        $this->link = $log->link;
        $this->is_published = $log->is_published;
    }

    public function updateLog()
    {
        $this->validate();

        $log = LogEntry::findOrFail($this->editingLogId);
        $log->update([
            'text' => $this->text,
            'link' => $this->link,
            'is_published' => $this->is_published,
        ]);

        $this->resetInput();
        $this->editingLogId = null;
        $this->loadLogs();
        session()->flash('message', 'Log entry updated!');
    }

    public function confirmDelete($id)
    {
        $this->confirmingDeletionId = $id;
    }

    public function deleteLog()
    {
        if (!$this->confirmingDeletionId) {
            return;
        }

        LogEntry::findOrFail($this->confirmingDeletionId)->delete();

        $this->confirmingDeletionId = null;
        $this->loadLogs();
        session()->flash('message', 'Log entry deleted!');
    }

    public function cancel()
    {
        $this->resetInput();
        $this->editingLogId = null;
        $this->confirmingDeletionId = null;
    }

    private function resetInput()
    {
        $this->text = '';
        $this->link = '';
        $this->is_published = true;
    }

    public function render()
    {
        return view('livewire.admin.log-entry-manager');
    }
}
