<?php

namespace App\Livewire\Admin\Zones;

use App\Models\AdministrativeZone;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Zonas administrativas')]
class Index extends Component
{
    #[On('zone-deleted')]
    public function onZoneDeleted(): void
    {
        //
    }

    public function mount(): void
    {
        if (! auth()->user()->isSuperAdmin() && ! auth()->user()->business_id) {
            abort(403, 'No tienes un negocio asignado para consultar zonas.');
        }
    }

    public function render()
    {
        $query = AdministrativeZone::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];

        return view('livewire.admin.zones.index', compact('stats'));
    }
}
