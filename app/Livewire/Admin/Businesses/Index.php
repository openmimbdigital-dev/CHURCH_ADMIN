<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Negocios')]
class Index extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()->can('businesses.view'), 403);
    }

    public function render()
    {
        $query = Business::query()->orderByDesc('id');

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('is_active', true)->count(),
        ];

        return view('livewire.admin.businesses.index', compact('stats'));
    }
}
