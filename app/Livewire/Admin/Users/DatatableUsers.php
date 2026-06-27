<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\FormatsDatatableActionsColumn;
use App\Models\Business;
use App\Models\User;
use Arm092\LivewireDatatables\Column;
use Arm092\LivewireDatatables\DateColumn;
use Arm092\LivewireDatatables\Livewire\LivewireDatatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class DatatableUsers extends LivewireDatatable
{
    use FormatsDatatableActionsColumn;

    public bool $exportable = true;

    public ?int $perPage = 25;

    public int $deleteId = 0;

    public function builder(): Builder
    {
        return User::query()
            ->visibleToAuth()
            ->orderByDesc('id');
    }

    public function getColumns(): Model|array
    {
        return [
            Column::name('username')
                ->label('Usuario')
                ->searchable()
                ->sortable(),

            Column::callback(['first_name', 'last_name'], function ($firstName, $lastName) {
                return e(trim($firstName.' '.$lastName));
            }, [], 'full_name')
                ->label('Nombre')
                ->searchable(),

            Column::name('email')
                ->label('Correo')
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



            Column::callback(['id'], function ($id) {
                $user = User::query()->visibleToAuth()->find($id);
                $role = $user?->roleLabelForViewer();

                return $role
                    ? '<span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-medium text-indigo-700 ring-1 ring-indigo-600/20">'.e($role).'</span>'
                    : '<span class="text-slate-400">—</span>';
            }, [], 'role')
                ->label('Rol'),

            Column::callback(['status'], function ($status) {
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

            Column::callback(['id'], function ($id) {
                $user = User::query()->visibleToAuth()->find($id);

                return view('livewire.admin.users.actions', [
                    'id' => $id,
                    'canView' => auth()->user()?->can('users.view') ?? false,
                    'canEdit' => auth()->user()?->can('users.edit') ?? false,
                    'canDelete' => auth()->user()?->can('users.delete') ?? false,
                    'isDeletable' => $user ? static::isUserDeletable($user) : false,
                    'deleteBlockReason' => $user ? static::userDeleteBlockReason($user) : null,
                ]);
            }, [], 'actions')
                ->label('Acciones')
                ->unsortable(),
        ];
    }

    public function deleteUser(int $id): void
    {
        if (! auth()->user()->can('users.delete')) {
            LivewireAlert::title('Sin permiso')
                ->text('No tienes permiso para eliminar usuarios.')
                ->error()
                ->asToast()
                ->show();

            return;
        }

        $this->deleteId = $id;

        LivewireAlert::title('Confirmar eliminación')
            ->text('¿Estás seguro de querer eliminar este usuario? Esta acción no se puede deshacer.')
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
            $user = $this->findAuthorizedUser($this->deleteId);

            if (! static::isUserDeletable($user)) {
                LivewireAlert::title('No permitido')
                    ->text(static::userDeleteBlockReason($user) ?? 'Este usuario no puede eliminarse.')
                    ->warning()
                    ->asToast()
                    ->show();

                return;
            }

            $user->delete();

            LivewireAlert::title('Usuario eliminado')
                ->text('El usuario fue eliminado correctamente.')
                ->success()
                ->asToast()
                ->show();

            $this->dispatch('user-deleted');
        } catch (\Throwable) {
            LivewireAlert::title('Error')
                ->text('No se pudo eliminar el usuario.')
                ->error()
                ->asToast()
                ->show();
        }
    }

    protected function canDeleteUser(?User $user): bool
    {
        return static::isUserDeletable($user);
    }

    protected static function isUserDeletable(?User $user): bool
    {
        return blank(static::userDeleteBlockReason($user));
    }

    protected static function userDeleteBlockReason(?User $user): ?string
    {
        if (! $user || ! auth()->user()?->can('users.delete')) {
            return 'No tienes permiso para eliminar usuarios.';
        }

        if (! $user->isVisibleToAuthUser()) {
            return 'No tienes permiso para eliminar este usuario.';
        }

        if ($user->id === auth()->id()) {
            return 'No puedes eliminar tu propio usuario.';
        }

        if ($user->hasRole('superAdmin')) {
            return 'El usuario superAdmin no puede eliminarse.';
        }

        return null;
    }

    protected function findAuthorizedUser(int $id): User
    {
        return User::query()
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
