<?php

namespace App\Livewire\Admin\Churches;

use App\Models\Church;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Iglesias')]
class Index extends Component
{
    #[On('church-deleted')]
    public function onChurchDeleted(): void
    {
        //
    }

    public function mount(): void
    {
        if (! auth()->user()->isSuperAdmin() && ! auth()->user()->business_id) {
            abort(403, 'No tienes un negocio asignado para consultar iglesias.');
        }
    }

    public function render()
    {
        $query = Church::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];

        return view('livewire.admin.churches.index', compact('stats'));
    }
}
