<?php

namespace App\Livewire\Admin\Settings\Events\Categories;

use App\Enums\EventCategoryType;
use App\Livewire\Concerns\FormatsDatatableActionsColumn;
use App\Models\ChurchEventCategory;
use App\Models\Event;
use App\Models\EventCategory;
use Arm092\LivewireDatatables\Column;
use Arm092\LivewireDatatables\DateColumn;
use Arm092\LivewireDatatables\Livewire\LivewireDatatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class DatatableEventCategories extends LivewireDatatable
{
    use FormatsDatatableActionsColumn;

    public bool $exportable = true;

    public ?int $perPage = 25;

    public int $deleteId = 0;

    public function builder(): Builder
    {
        return EventCategory::query()
            ->visibleToAuth()
            ->orderByDesc('id');
    }

    public function getColumns(): Model|array
    {
        return [
            Column::name('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

            Column::callback(['type'], function ($type) {
                $enum = EventCategoryType::tryFrom((string) $type);
                $label = $enum?->label() ?? '—';
                $class = $enum === EventCategoryType::Periodico
                    ? 'bg-sky-50 text-sky-700 ring-1 ring-sky-600/20'
                    : 'bg-violet-50 text-violet-700 ring-1 ring-violet-600/20';

                return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium '.$class.'">'.e($label).'</span>';
            }, [], 'type_badge')
                ->label('Tipo')
                ->filterable(EventCategoryType::options()),

            Column::callback(['general'], function ($general) {
                if ($general) {
                    return '<span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 ring-1 ring-primary-600/20">General</span>';
                }

                return '<span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-500/20">Por iglesia</span>';
            }, [], 'scope_badge')
                ->label('Ámbito')
                ->filterable([1 => 'General', 0 => 'Por iglesia']),

            Column::callback(['id', 'general'], function ($id, $general) {
                if ($general) {
                    return '<span class="text-slate-400">Todas</span>';
                }

                static $labels = null;

                if ($labels === null) {
                    $query = ChurchEventCategory::query()
                        ->join('churches', 'churches.id', '=', 'church_event_category.church_id')
                        ->select([
                            'church_event_category.event_category_id',
                            'churches.name as church_name',
                        ]);

                    $viewer = auth()->user();
                    if ($viewer && ! $viewer->isSuperAdmin() && $viewer->business_id) {
                        $query->where('church_event_category.business_id', $viewer->business_id);
                    }

                    $labels = $query->get()
                        ->groupBy('event_category_id')
                        ->map(fn ($rows) => $rows->pluck('church_name')->filter()->unique()->implode(', '))
                        ->all();
                }

                $label = $labels[$id] ?? null;

                if (! $label) {
                    return '<span class="text-slate-400">—</span>';
                }

                return e($label);
            }, [], 'churches')
                ->label('Iglesias')
                ->unsortable(),

            Column::callback(['id'], function ($id) {
                static $counts = null;

                if ($counts === null) {
                    $query = Event::query();

                    $viewer = auth()->user();
                    if ($viewer && ! $viewer->isSuperAdmin() && $viewer->business_id) {
                        $query->where('business_id', $viewer->business_id);
                    }

                    $counts = $query
                        ->selectRaw('event_category_id, COUNT(*) as total')
                        ->groupBy('event_category_id')
                        ->pluck('total', 'event_category_id')
                        ->all();
                }

                return '<span class="tabular-nums">'.(int) ($counts[$id] ?? 0).'</span>';
            }, [], 'events_count')
                ->label('Eventos')
                ->unsortable(),

            Column::callback(['active'], function ($status) {
                $label = $status ? 'Activa' : 'Inactiva';
                $class = $status
                    ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20'
                    : 'bg-slate-100 text-slate-600 ring-1 ring-slate-500/20';

                return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium '.$class.'">'.$label.'</span>';
            }, [], 'status_badge')
                ->label('Estado')
                ->filterable([1 => 'Activa', 0 => 'Inactiva']),

            DateColumn::name('created_at')
                ->label('Registro')
                ->sortable(),

            Column::callback(['id'], function ($id) {
                $category = EventCategory::query()->visibleToAuth()->whereKey($id)->first();
                $viewer = auth()->user();
                $canManage = $category?->isManagedByAuthUser($viewer) ?? false;
                $hasDeletePermission = $viewer?->can('settings.event.category.delete') ?? false;
                $isGeneral = (bool) ($category?->general ?? false);

                return view('livewire.admin.settings.events.categories.actions', [
                    'id' => $id,
                    'canView' => $viewer?->can('settings.event.category.view') ?? false,
                    'canEdit' => ($viewer?->can('settings.event.category.edit') ?? false) && $canManage,
                    'canDelete' => $hasDeletePermission && $canManage,
                    'showDeleteDisabled' => $hasDeletePermission && $isGeneral && ! ($viewer?->isSuperAdmin() ?? false),
                    'deleteDisabledReason' => 'Solo el super administrador puede eliminar categorías generales.',
                    'isDeletable' => $category?->canBeDeleted() ?? false,
                    'deleteBlockReason' => $category?->deletionBlockedReason(),
                ]);
            }, [], 'actions')
                ->label('Acciones')
                ->unsortable(),
        ];
    }

    public function deleteEventCategory(int $id): void
    {
        if (! auth()->user()->can('settings.event.category.delete')) {
            LivewireAlert::title('Sin permiso')
                ->text('No tienes permiso para eliminar categorías de eventos.')
                ->error()
                ->asToast()
                ->show();

            return;
        }

        $this->deleteId = $id;

        LivewireAlert::title('Confirmar eliminación')
            ->text('¿Estás seguro de querer eliminar esta categoría? Esta acción no se puede deshacer.')
            ->warning()
            ->withConfirmButton('Eliminar')
            ->withCancelButton('Cancelar')
            ->confirmButtonColor('#e11d48')
            ->cancelButtonColor('#64748b')
            ->customClass([
                'popup' => 'swal-church-popup',
                'title' => 'swal-church-title',
                'htmlContainer' => 'swal-church-html',
                'confirmButton' => 'swal-confirm-button',
                'cancelButton' => 'swal-cancel-button',
            ])
            ->onConfirm('confirmed')
            ->show();
    }

    public function confirmed(): void
    {
        try {
            $category = $this->findAuthorizedEventCategory($this->deleteId);

            if (! $category->isManagedByAuthUser()) {
                LivewireAlert::title('Sin permiso')
                    ->text($category->general && ! auth()->user()->isSuperAdmin()
                        ? 'Solo el super administrador puede eliminar categorías generales.'
                        : 'No puedes eliminar esta categoría.')
                    ->warning()
                    ->asToast()
                    ->show();

                return;
            }

            if ($reason = $category->deletionBlockedReason()) {
                LivewireAlert::title('No se puede eliminar')
                    ->text($reason)
                    ->warning()
                    ->asToast()
                    ->show();

                return;
            }

            $category->delete();

            LivewireAlert::title('Categoría eliminada')
                ->text('La categoría fue eliminada correctamente.')
                ->success()
                ->asToast()
                ->show();

            $this->dispatch('event-category-deleted');
        } catch (\Throwable) {
            LivewireAlert::title('Error')
                ->text('No se pudo eliminar la categoría.')
                ->error()
                ->asToast()
                ->show();
        }
    }

    protected function findAuthorizedEventCategory(int $id): EventCategory
    {
        return EventCategory::query()
            ->visibleToAuth()
            ->whereKey($id)
            ->firstOrFail();
    }

    public function render()
    {
        $this->dispatch('refreshDynamic');

        if ($this->persistPerPage) {
            session()->put([$this->sessionStorageKey().'_perpage' => $this->perPage]);
        }

        return view('datatables::datatable');
    }
}
