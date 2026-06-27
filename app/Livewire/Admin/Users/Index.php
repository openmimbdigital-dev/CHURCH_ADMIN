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

    #[On('church-switched')]
    public function onChurchSwitched(): void
    {
        //
    }

    public function mount(): void
    {
        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado para consultar usuarios.');
        }

        if (! $viewer->isSuperAdmin() && ! $viewer->current_church_id) {
            abort(403, 'Debes seleccionar una iglesia activa para consultar usuarios.');
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
