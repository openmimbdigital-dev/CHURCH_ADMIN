<?php

namespace App\Livewire\Forms\EventCategories;

use App\Actions\EventCategory\CreateOrUpdateEventCategory;
use App\Enums\EventCategoryType;
use App\Models\Church;
use App\Models\EventCategory;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Livewire\Form;

class EventCategoryForm extends Form
{
    public string $name = '';

    public ?string $description = null;

    public string $type = '';

    public bool $active = true;

    public bool $general = false;

    /** @var array<int, int> */
    public array $selected_business_ids = [];

    /** @var array<int, int> */
    public array $selected_zone_ids = [];

    /** @var array<int, int> */
    public array $selected_church_ids = [];

    public function mountDefaults(): void
    {
        if ($this->type === '') {
            $this->type = EventCategoryType::Periodico->value;
        }
    }

    public function fillFromCategory(EventCategory $category): void
    {
        $this->name = $category->name;
        $this->description = $category->description;
        $this->type = $category->type instanceof EventCategoryType
            ? $category->type->value
            : (string) $category->type;
        $this->active = (bool) $category->active;
        $this->general = (bool) $category->general;

        $churchIds = $category->churchEventCategories()
            ->pluck('church_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $this->selected_church_ids = $churchIds;

        if ($churchIds !== []) {
            $churches = Church::query()
                ->whereIn('id', $churchIds)
                ->get(['id', 'business_id', 'administrative_zone_id']);

            $this->selected_business_ids = $churches->pluck('business_id')
                ->map(fn ($id) => (int) $id)
                ->unique()
                ->values()
                ->all();

            $this->selected_zone_ids = $churches->pluck('administrative_zone_id')
                ->map(fn ($id) => (int) $id)
                ->filter()
                ->unique()
                ->values()
                ->all();
        }
    }

    public function applyActorDefaults(User $actor): void
    {
        if ($actor->isSuperAdmin()) {
            return;
        }

        $this->general = false;
        $this->selected_business_ids = [];

        if ($actor->isPresbitero()) {
            $this->selected_zone_ids = $actor->churchZoneIds();

            if ($actor->can('churches.viewAll')) {
                $this->selected_church_ids = [];
            } elseif ($actor->current_church_id) {
                $this->selected_church_ids = [(int) $actor->current_church_id];
            }

            return;
        }

        $this->selected_zone_ids = [];

        if ($actor->current_church_id) {
            $this->selected_church_ids = [(int) $actor->current_church_id];
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function validateFor(?EventCategory $category, User $actor): array
    {
        if (! $actor->isSuperAdmin()) {
            $this->general = false;
            $this->selected_business_ids = [];
            $this->applyActorDefaults($actor);
        }

        $validated = $this->validate($this->rules($category, $actor));

        $isGeneral = $actor->isSuperAdmin() && $this->general;
        $nameError = EventCategory::nameUniquenessError(
            $this->name,
            $isGeneral,
            $this->selected_church_ids,
            $category?->id,
        );

        if ($nameError !== null) {
            throw ValidationException::withMessages([
                'categoryForm.name' => $nameError,
            ]);
        }

        $validated['name'] = trim($this->name);
        $validated['general'] = $isGeneral;
        $validated['church_ids'] = $isGeneral ? [] : $this->selected_church_ids;

        return $validated;
    }

    public function save(?EventCategory $category, User $actor): EventCategory
    {
        $validated = $this->validateFor($category, $actor);

        return app(CreateOrUpdateEventCategory::class)->handle($validated, $category, $actor);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(?EventCategory $category, User $actor): array
    {
        $isGeneral = $actor->isSuperAdmin() && $this->general;

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'type' => ['required', 'string', Rule::in(array_column(EventCategoryType::cases(), 'value'))],
            'active' => ['boolean'],
        ];

        if ($actor->isSuperAdmin()) {
            $rules['general'] = ['boolean'];
        }

        if (! $isGeneral) {
            $rules['selected_church_ids'] = ['required', 'array', 'min:1'];
            $rules['selected_church_ids.*'] = ['integer', 'distinct'];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.max' => 'El nombre no puede superar 255 caracteres.',
            'description.max' => 'La descripción no puede superar 2000 caracteres.',
            'type.required' => 'El tipo de categoría es obligatorio.',
            'type.in' => 'El tipo de categoría seleccionado no es válido.',
            'selected_church_ids.required' => 'Debes seleccionar al menos una iglesia.',
            'selected_church_ids.min' => 'Debes seleccionar al menos una iglesia.',
        ];
    }
}
