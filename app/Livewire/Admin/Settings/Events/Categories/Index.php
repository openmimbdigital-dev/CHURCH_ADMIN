<?php

namespace App\Livewire\Admin\Settings\Events\Categories;

use App\Livewire\Admin\Settings\Events\EventSettingsConfig;
use App\Models\EventCategory;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Categorías de eventos')]
class Index extends Component
{
    #[On('event-category-deleted')]
    public function onEventCategoryDeleted(): void
    {
        //
    }

    public function mount(): void
    {
        if (! auth()->user()->can('settings.event.category.view')) {
            abort(403);
        }

        $viewer = auth()->user();

        if (! $viewer->isSuperAdmin() && ! $viewer->business_id) {
            abort(403, 'No tienes un negocio asignado.');
        }

        if (! $viewer->isSuperAdmin() && ! $viewer->current_church_id && ! $viewer->can('churches.viewAll')) {
            abort(403, 'Debes seleccionar una iglesia activa.');
        }
    }

    public function render()
    {
        $query = EventCategory::query()->visibleToAuth();

        $stats = [
            'total' => (clone $query)->count(),
            'active' => (clone $query)->where('active', true)->count(),
            'general' => (clone $query)->where('general', true)->count(),
        ];

        return view('livewire.admin.settings.events.categories.index', [
            'config' => EventSettingsConfig::sectionOrFail('categories'),
            'stats' => $stats,
        ]);
    }
}
