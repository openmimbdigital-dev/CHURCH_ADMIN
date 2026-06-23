<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Usuarios')]
class Index extends Component
{
    #[On('user-deleted')]
    public function onUserDeleted(): void
    {
        //
    }

    public function mount(): void
    {
        if (! auth()->user()->isSuperAdmin() && ! auth()->user()->business_id) {
            abort(403, 'No tienes un negocio asignado para consultar usuarios.');
        }
    }

    public function render()
    {
        $query = User::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('status', true)->count(),
        ];

        return view('livewire.admin.users.index', compact('stats'));
    }
}
