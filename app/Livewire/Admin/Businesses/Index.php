<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Negocios')]
class Index extends Component
{
    #[On('business-deleted')]
    public function onBusinessDeleted(): void
    {
        //
    }

    public function mount(): void
    {
        abort_unless(auth()->user()->can('businesses.view'), 403);
        abort_unless(auth()->user()->isSuperAdmin(), 403);
    }

    public function render()
    {
        $query = Business::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];

        return view('livewire.admin.businesses.index', compact('stats'));
    }
}
