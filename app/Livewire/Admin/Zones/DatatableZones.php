<?php

namespace App\Livewire\Admin\Zones;

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

class DatatableZones extends LivewireDatatable
{
    use FormatsDatatableActionsColumn;

    public bool $exportable = true;

    public ?int $perPage = 25;

    public int $deleteId = 0;

    public function builder(): Builder
    {
        return AdministrativeZone::query()
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

            Column::callback(['city_id'], function ($cityId) {
                static $cityLabels = [];

                if (! $cityId) {
                    return '<span class="text-slate-400">—</span>';
                }

                if (! array_key_exists($cityId, $cityLabels)) {
                    $city = City::query()->with('department:id,name')->find($cityId);
                    $cityLabels[$cityId] = $city
                        ? trim($city->name.($city->department ? ' — '.$city->department->name : ''))
                        : '—';
                }

                return e($cityLabels[$cityId] ?? '—');
            }, [], 'city')
                ->label('Ciudad'),

            Column::callback(['id'], function ($id) {
                static $counts = null;

                if ($counts === null) {
                    $query = Church::query();

                    $viewer = auth()->user();
                    if ($viewer && ! $viewer->isSuperAdmin() && $viewer->business_id) {
                        $query->where('business_id', $viewer->business_id);
                    }

                    $counts = $query
                        ->selectRaw('administrative_zone_id, COUNT(*) as total')
                        ->groupBy('administrative_zone_id')
                        ->pluck('total', 'administrative_zone_id')
                        ->all();
                }

                return '<span class="tabular-nums">'.(int) ($counts[$id] ?? 0).'</span>';
            }, [], 'churches_count')
                ->label('Iglesias')
                ->unsortable(),

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
                $zone = AdministrativeZone::query()->find($id);

                return view('livewire.admin.zones.actions', [
                    'id' => $id,
                    'canView' => auth()->user()?->can('zones.view') ?? false,
                    'canEdit' => auth()->user()?->can('zones.edit') ?? false,
                    'canDelete' => auth()->user()?->can('zones.delete') ?? false,
                    'isDeletable' => $zone?->canBeDeleted() ?? false,
                    'deleteBlockReason' => $zone?->deletionBlockedReason(),
                ]);
            }, [], 'actions')
                ->label('Acciones')
                ->unsortable(),
        ];
    }

    public function deleteZone(int $id): void
    {
        if (! auth()->user()->can('zones.delete')) {
            LivewireAlert::title('Sin permiso')
                ->text('No tienes permiso para eliminar zonas.')
                ->error()
                ->asToast()
                ->show();

            return;
        }

        $this->deleteId = $id;

        LivewireAlert::title('Confirmar eliminación')
            ->text('¿Estás seguro de querer eliminar esta zona? Esta acción no se puede deshacer.')
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
            $zone = $this->findAuthorizedZone($this->deleteId);

            if ($reason = $zone->deletionBlockedReason()) {
                LivewireAlert::title('No se puede eliminar')
                    ->text($reason)
                    ->warning()
                    ->asToast()
                    ->show();

                return;
            }

            $zone->delete();

            LivewireAlert::title('Zona eliminada')
                ->text('La zona fue eliminada correctamente.')
                ->success()
                ->asToast()
                ->show();

            $this->dispatch('zone-deleted');
        } catch (\Throwable) {
            LivewireAlert::title('Error')
                ->text('No se pudo eliminar la zona.')
                ->error()
                ->asToast()
                ->show();
        }
    }

    protected function findAuthorizedZone(int $id): AdministrativeZone
    {
        return AdministrativeZone::query()
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
