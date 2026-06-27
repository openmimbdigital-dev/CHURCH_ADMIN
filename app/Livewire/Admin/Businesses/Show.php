<?php

namespace App\Livewire\Admin\Businesses;

use App\Models\Business;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public Business $business;

    public function mount(Business $business): void
    {
        abort_unless(auth()->user()->can('businesses.view'), 403);
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        $this->business = $business->load([
            'city.department:id,name',
        ])->loadCount(['users', 'administrativeZones', 'churches']);
    }

    public function render()
    {
        return view('livewire.admin.businesses.show')
            ->title('Negocio: '.$this->business->name);
    }
}
