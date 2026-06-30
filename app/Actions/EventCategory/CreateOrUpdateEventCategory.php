<?php

namespace App\Actions\EventCategory;

use App\Models\Church;
use App\Models\ChurchEventCategory;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateOrUpdateEventCategory
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data, ?EventCategory $category, User $actor): EventCategory
    {
        return DB::transaction(function () use ($data, $category, $actor) {
            $general = $actor->isSuperAdmin() && (bool) ($data['general'] ?? false);

            $eventCategory = EventCategory::updateOrCreate(
                ['id' => $category?->id],
                [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? null,
                    'type' => $data['type'],
                    'active' => (bool) ($data['active'] ?? true),
                    'general' => $general,
                ]
            );

            ChurchEventCategory::query()
                ->where('event_category_id', $eventCategory->id)
                ->delete();

            if (! $general) {
                $this->syncChurchAssignments($eventCategory, $data['church_ids'] ?? [], $actor);
            }

            return $eventCategory->fresh();
        });
    }

    /**
     * @param  array<int, int|string>  $churchIds
     */
    protected function syncChurchAssignments(EventCategory $category, array $churchIds, User $actor): void
    {
        $churchIds = collect($churchIds)
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values()
            ->all();

        if ($churchIds === []) {
            throw ValidationException::withMessages([
                'categoryForm.selected_church_ids' => 'Debes seleccionar al menos una iglesia.',
            ]);
        }

        $query = Church::query()->whereIn('id', $churchIds);

        if (! $actor->isSuperAdmin()) {
            $query->visibleToAuth();
        }

        $churches = $query->get(['id', 'business_id']);

        if ($churches->count() !== count($churchIds)) {
            throw ValidationException::withMessages([
                'categoryForm.selected_church_ids' => 'Una o más iglesias seleccionadas no son válidas.',
            ]);
        }

        foreach ($churches as $church) {
            ChurchEventCategory::query()->create([
                'event_category_id' => $category->id,
                'church_id' => $church->id,
                'business_id' => $church->business_id,
            ]);
        }
    }
}
