<?php

namespace App\Livewire\Admin\Settings\Events\Categories;

use App\Livewire\Forms\EventCategories\EventCategoryForm;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\EventCategory;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?EventCategory $eventCategory = null;

    public EventCategoryForm $categoryForm;

    /** @var Collection<int, Business> */
    public Collection $businessHierarchy;

    /** @var Collection<int, AdministrativeZone> */
    public Collection $actorZones;

    /** @var Collection<int, Church> */
    public Collection $actorChurches;

    public function mount(?EventCategory $eventCategory = null): void
    {
        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado.');
        }

        if (! $viewer->isSuperAdmin() && ! $viewer->current_church_id && ! $viewer->can('churches.viewAll')) {
            abort(403, 'Debes seleccionar una iglesia activa.');
        }

        if ($eventCategory) {
            if (! $viewer->can('settings.event.category.edit')) {
                abort(403);
            }

            if (! $eventCategory->isVisibleToAuthUser($viewer)) {
                abort(403);
            }

            if (! $eventCategory->isManagedByAuthUser($viewer)) {
                abort(403, 'No tienes permiso para editar esta categoría.');
            }

            $this->eventCategory = $eventCategory;
            $this->categoryForm->fillFromCategory($eventCategory);
        } else {
            if (! $viewer->can('settings.event.category.create')) {
                abort(403);
            }

            $this->categoryForm->mountDefaults();
            $this->categoryForm->applyActorDefaults($viewer);
        }

        $this->businessHierarchy = $viewer->isSuperAdmin()
            ? Business::query()
                ->where('is_active', true)
                ->with([
                    'administrativeZones' => fn ($query) => $query
                        ->where('is_active', true)
                        ->orderBy('name')
                        ->with([
                            'churches' => fn ($churchQuery) => $churchQuery
                                ->where('is_active', true)
                                ->orderBy('name'),
                        ]),
                ])
                ->orderBy('name')
                ->get()
            : collect();

        $this->loadActorAssignmentOptions($viewer);
    }

    protected function loadActorAssignmentOptions($viewer): void
    {
        if ($viewer->isSuperAdmin()) {
            $this->actorZones = collect();
            $this->actorChurches = collect();

            return;
        }

        $zonesQuery = AdministrativeZone::query()
            ->where('business_id', $viewer->business_id)
            ->where('is_active', true)
            ->orderBy('name');

        if ($viewer->isPresbitero() && ! $viewer->can('churches.viewAll')) {
            $zonesQuery->whereIn('id', $viewer->churchZoneIds());
        }

        $this->actorZones = $zonesQuery->get(['id', 'name', 'business_id']);

        $churchesQuery = Church::query()
            ->visibleToAuth($viewer)
            ->where('is_active', true)
            ->orderBy('name');

        if ($viewer->isPresbitero() && $this->categoryForm->selected_zone_ids !== []) {
            $churchesQuery->whereIn('administrative_zone_id', $this->categoryForm->selected_zone_ids);
        }

        $this->actorChurches = $churchesQuery->get(['id', 'name', 'administrative_zone_id', 'business_id']);
    }

    public function updatedCategoryFormSelectedBusinessIds(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            return;
        }

        $allowedZoneIds = $this->businessHierarchy
            ->whereIn('id', $this->categoryForm->selected_business_ids)
            ->flatMap(fn (Business $business) => $business->administrativeZones->pluck('id'))
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->categoryForm->selected_zone_ids = array_values(array_intersect(
            $this->categoryForm->selected_zone_ids,
            $allowedZoneIds
        ));

        $this->updatedCategoryFormSelectedZoneIds();
    }

    public function updatedCategoryFormSelectedZoneIds(): void
    {
        $viewer = auth()->user();

        if ($viewer->isSuperAdmin()) {
            $allowedChurchIds = $this->businessHierarchy
                ->flatMap(fn (Business $business) => $business->administrativeZones)
                ->whereIn('id', $this->categoryForm->selected_zone_ids)
                ->flatMap(fn (AdministrativeZone $zone) => $zone->churches->pluck('id'))
                ->map(fn ($id) => (int) $id)
                ->all();

            $this->categoryForm->selected_church_ids = array_values(array_intersect(
                $this->categoryForm->selected_church_ids,
                $allowedChurchIds
            ));

            return;
        }

        if ($viewer->isPresbitero()) {
            $this->loadActorAssignmentOptions($viewer);

            $allowedChurchIds = $this->actorChurches->pluck('id')->map(fn ($id) => (int) $id)->all();

            $this->categoryForm->selected_church_ids = array_values(array_intersect(
                $this->categoryForm->selected_church_ids,
                $allowedChurchIds
            ));
        }
    }

    public function updatedCategoryFormGeneral(bool $general): void
    {
        if ($general) {
            $this->categoryForm->selected_business_ids = [];
            $this->categoryForm->selected_zone_ids = [];
            $this->categoryForm->selected_church_ids = [];
        }
    }

    public function save(): void
    {
        $this->categoryForm->save($this->eventCategory, auth()->user());

        LivewireAlert::title($this->eventCategory ? 'Categoría actualizada' : 'Categoría creada')
            ->text($this->eventCategory
                ? 'Los datos de la categoría se guardaron correctamente.'
                : 'La categoría fue registrada correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->redirect(route('admin.settings.events.categories'), navigate: true);
    }

    public function render()
    {
        $viewer = auth()->user()->load('currentChurch');

        return view('livewire.admin.settings.events.categories.form', [
            'typeOptions' => \App\Enums\EventCategoryType::options(),
            'isSuperAdmin' => $viewer->isSuperAdmin(),
            'isPresbitero' => $viewer->isPresbitero(),
            'viewer' => $viewer,
        ])->title($this->eventCategory ? 'Editar categoría' : 'Crear categoría');
    }
}
