<?php

namespace App\Livewire\Admin\Churches;

use App\Models\Church;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Church $church;

    public function mount(Church $church): void
    {
        if (! auth()->user()->can('churches.view')) {
            abort(403);
        }

        if (! $church->isVisibleToAuthUser()) {
            abort(403);
        }

        $this->church = $church->load([
            'business:id,name',
            'administrativeZone:id,name',
            'parent:id,name',
            'city.department:id,name',
            'children' => fn ($q) => $q->orderBy('name'),
            'leaders:id,first_name,last_name,username,email',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.churches.show')
            ->title('Iglesia: '.$this->church->name);
    }
}
