<?php

namespace App\Livewire\Admin\Events;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Configuración de eventos y reuniones')]
class Index extends Component
{
    public function mount(): void
    {
        if (! auth()->user()->can('settings.event.view')) {
            abort(403);
        }

        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado para consultar eventos.');
        }

        if (! $viewer->isSuperAdmin() && ! $viewer->current_church_id) {
            abort(403, 'Debes seleccionar una iglesia activa para consultar eventos.');
        }
    }

    public function render()
    {
        $query = Event::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('active', true)->count(),
        ];

        return view('livewire.admin.events.index', compact('stats'));
    }
}
