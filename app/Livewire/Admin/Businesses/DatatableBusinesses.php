<?php

namespace App\Livewire\Admin\Businesses;

use App\Livewire\Concerns\FormatsDatatableActionsColumn;
use App\Models\Business;
use App\Models\City;
use Arm092\LivewireDatatables\Column;
use Arm092\LivewireDatatables\DateColumn;
use Arm092\LivewireDatatables\Livewire\LivewireDatatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class DatatableBusinesses extends LivewireDatatable
{
    use FormatsDatatableActionsColumn;

    public bool $exportable = true;

    public ?int $perPage = 25;

    public function builder(): Builder
    {
        return Business::query()->orderByDesc('id');
    }

    public function getColumns(): Model|array
    {
        return [
            Column::name('name')
                ->label('Nombre')
                ->searchable()
                ->sortable(),

            Column::name('nit')
                ->label('NIT')
                ->searchable()
                ->sortable(),

            Column::name('email')
                ->label('Correo')
                ->searchable(),

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

            Column::callback(['is_active'], function ($status) {
                $label = $status ? 'Activo' : 'Inactivo';
                $class = $status
                    ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/20'
                    : 'bg-slate-100 text-slate-600 ring-1 ring-slate-500/20';

                return '<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium '.$class.'">'.$label.'</span>';
            }, [], 'status_badge')
                ->label('Estado')
                ->filterable([1 => 'Activo', 0 => 'Inactivo']),

            DateColumn::name('created_at')
                ->label('Registro')
                ->sortable(),
        ];
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
