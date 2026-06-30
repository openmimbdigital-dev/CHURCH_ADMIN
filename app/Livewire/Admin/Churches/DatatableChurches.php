<?php

namespace App\Livewire\Admin\Churches;

use App\Enums\ChurchCategory;
use App\Livewire\Concerns\FormatsDatatableActionsColumn;
use App\Models\AdministrativeZone;
use App\Models\Business;
use App\Models\Church;
use App\Models\City;
use Arm092\LivewireDatatables\Column;
use Arm092\LivewireDatatables\DateColumn;
use Arm092\LivewireDatatables\Livewire\LivewireDatatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class DatatableChurches extends LivewireDatatable
{
    use FormatsDatatableActionsColumn;

    public bool $exportable = true;

    public ?int $perPage = 25;

    public int $deleteId = 0;

    public function builder(): Builder
    {
        return Church::query()
            ->visibleToAuth()
            ->with(['business:id,name', 'administrativeZone:id,name'])
            ->orderByDesc('id');
    }

    public function getColumns(): Model|array
    {
        return [
            Column::name('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

            Column::callback(['business_id'], function ($businessId) {
                static $businessNames = [];

                if (! $businessId) {
                    return '<span class="text-slate-400">—</span>';
                }

                if (! array_key_exists($businessId, $businessNames)) {
                    $businessNames[$businessId] = Business::query()->whereKey($businessId)->value('name');
                }

                return e($businessNames[$businessId] ?? '—');
            }, [], 'business')
                ->label('Negocio'),

            Column::callback(['administrative_zone_id'], function ($zoneId) {
                static $zoneNames = [];

                if (! $zoneId) {
                    return '<span class="text-slate-400">—</span>';
                }

                if (! array_key_exists($zoneId, $zoneNames)) {
                    $zoneNames[$zoneId] = AdministrativeZone::query()->whereKey($zoneId)->value('name');
                }

                return e($zoneNames[$zoneId] ?? '—');
            }, [], 'zone')
                ->label('Zona')
                ->searchable(),

            Column::callback(['category'], function ($category) {
                $enum = ChurchCategory::tryFrom($category);

                return $enum
                    ? '<span class="inline-flex items-center rounded-full bg-primary-50 px-2.5 py-1 text-xs font-medium text-primary-700 ring-1 ring-primary-600/20">'.e($enum->label()).'</span>'
                    : '<span class="text-slate-400">—</span>';
            }, [], 'category_badge')
                ->label('Categoría')
                ->filterable(collect(ChurchCategory::cases())->mapWithKeys(fn ($c) => [$c->value => $c->label()])->all()),

            Column::callback(['parent_id'], function ($parentId) {
                static $parentNames = [];

                if (! $parentId) {
                    return '<span class="text-slate-400">—</span>';
                }

                if (! array_key_exists($parentId, $parentNames)) {
                    $parentNames[$parentId] = Church::query()->whereKey($parentId)->value('name');
                }

                return e($parentNames[$parentId] ?? '—');
            }, [], 'parent')
                ->label('Iglesia madre'),

            Column::callback(['is_active'], function ($status) {
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
                $church = Church::query()->find($id);

                return view('livewire.admin.churches.actions', [
                    'id' => $id,
                    'canView' => auth()->user()?->can('churches.view') ?? false,
                    'canEdit' => auth()->user()?->can('churches.edit') ?? false,
                    'canDelete' => auth()->user()?->can('churches.delete') ?? false,
                    'isDeletable' => $church?->canBeDeleted() ?? false,
                    'deleteBlockReason' => $church?->deletionBlockedReason(),
                ]);
            }, [], 'actions')
                ->label('Acciones')
                ->unsortable(),
        ];
    }

    public function deleteChurch(int $id): void
    {
        if (! auth()->user()->can('churches.delete')) {
            LivewireAlert::title('Sin permiso')
                ->text('No tienes permiso para eliminar iglesias.')
                ->error()
                ->asToast()
                ->show();

            return;
        }

        $this->deleteId = $id;

        LivewireAlert::title('Confirmar eliminación')
            ->text('¿Estás seguro de querer eliminar esta iglesia? Esta acción no se puede deshacer.')
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
            $church = $this->findAuthorizedChurch($this->deleteId);

            if ($reason = $church->deletionBlockedReason()) {
                LivewireAlert::title('No se puede eliminar')
                    ->text($reason)
                    ->warning()
                    ->asToast()
                    ->show();

                return;
            }

            $church->delete();

            LivewireAlert::title('Iglesia eliminada')
                ->text('La iglesia fue eliminada correctamente.')
                ->success()
                ->asToast()
                ->show();

            $this->dispatch('church-deleted');
        } catch (\Throwable) {
            LivewireAlert::title('Error')
                ->text('No se pudo eliminar la iglesia.')
                ->error()
                ->asToast()
                ->show();
        }
    }

    protected function findAuthorizedChurch(int $id): Church
    {
        return Church::query()
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
