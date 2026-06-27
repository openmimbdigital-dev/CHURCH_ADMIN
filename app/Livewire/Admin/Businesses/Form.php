<?php

namespace App\Livewire\Admin\Businesses;

use App\Livewire\Forms\Businesses\BusinessForm;
use App\Models\Business;
use App\Models\City;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Business $business = null;

    public BusinessForm $businessForm;

    /** @var Collection<int, City> */
    public Collection $cities;

    public function mount(?Business $business = null): void
    {
        abort_unless(auth()->user()->isSuperAdmin(), 403);

        if ($business) {
            abort_unless(auth()->user()->can('businesses.edit'), 403);
            $this->business = $business;
            $this->businessForm->fillFromBusiness($business);
        } else {
            abort_unless(auth()->user()->can('businesses.create'), 403);
        }

        $this->cities = City::query()
            ->with('department:id,name')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'department_id']);
    }

    public function save(): void
    {
        $this->businessForm->save($this->business, auth()->user());

        LivewireAlert::title($this->business ? 'Negocio actualizado' : 'Negocio creado')
            ->text($this->business
                ? 'Los datos del negocio se guardaron correctamente.'
                : 'El negocio fue registrado correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->redirect(route('admin.businesses.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.businesses.form')
            ->title($this->business ? 'Editar negocio' : 'Crear negocio');
    }
}
