<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public function render()
    {
        $users = User::latest()->paginate(100);

        return view('livewire.admin.users', [
            'users' => $users
        ]);
    }
}
