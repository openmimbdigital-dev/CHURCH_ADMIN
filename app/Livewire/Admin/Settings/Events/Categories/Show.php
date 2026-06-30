<?php

namespace App\Livewire\Admin\Settings\Events\Categories;

use App\Livewire\Admin\Settings\Events\EventSettingsConfig;
use App\Models\ChurchEventCategory;
use App\Models\EventCategory;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Show extends Component
{
    public EventCategory $eventCategory;

    /** @var Collection<int, ChurchEventCategory> */
    public Collection $assignments;

    public function mount(EventCategory $eventCategory): void
    {
        if (! auth()->user()->can('settings.event.category.view')) {
            abort(403);
        }

        if (! $eventCategory->isVisibleToAuthUser()) {
            abort(403);
        }

        $this->eventCategory = $eventCategory;

        $assignmentsQuery = ChurchEventCategory::query()
            ->where('event_category_id', $eventCategory->id)
            ->with([
                'church:id,name,administrative_zone_id',
                'church.administrativeZone:id,name',
                'business:id,name',
            ]);

        $viewer = auth()->user();
        if (! $viewer->isSuperAdmin() && $viewer->business_id) {
            $assignmentsQuery->where('business_id', $viewer->business_id);
        }

        $this->assignments = $assignmentsQuery->get();
    }

    public function render()
    {
        return view('livewire.admin.settings.events.categories.show', [
            'config' => EventSettingsConfig::sectionOrFail('categories'),
        ])->title('Categoría: '.$this->eventCategory->name);
    }
}
