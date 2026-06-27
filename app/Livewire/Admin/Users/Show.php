<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public User $user;

    public function mount(User $user): void
    {
        if (! auth()->user()->can('users.view')) {
            abort(403);
        }

        if (! auth()->user()->isSuperAdmin() && ! auth()->user()->current_church_id) {
            abort(403, 'Debes seleccionar una iglesia activa para consultar usuarios.');
        }

        if (! $user->isVisibleToAuthUser()) {
            abort(403);
        }

        $this->user = $user->load([
            'business:id,name',
            'roles:id,name',
            'administrativeZones:id,name',
            'churches' => fn ($query) => $query->orderBy('name'),
        ]);
    }

    public function render()
    {
        return view('livewire.admin.users.show')
            ->title('Usuario: '.$this->user->username);
    }
}
