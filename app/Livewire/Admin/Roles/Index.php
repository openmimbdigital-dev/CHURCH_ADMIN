<?php

namespace App\Livewire\Admin\Roles;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.app')]
#[Title('Roles y permisos')]
class Index extends Component
{
    const PROTECTED_ROLES = ['superAdmin'];

    public bool $showModal = false;

    public ?int $selected_id = null;

    public string $name = '';

    public array $selectedPerms = [];

    public string $search = '';

    public ?int $expandedRole = null;

    public ?int $deleteId = null;

    protected function rules(): array
    {
        $unique = $this->selected_id
            ? 'unique:roles,name,'.$this->selected_id
            : 'unique:roles,name';

        return [
            'name' => "required|string|max:50|{$unique}",
            'selectedPerms' => 'array',
        ];
    }

    protected $messages = [
        'name.required' => 'El nombre del rol es obligatorio.',
        'name.unique' => 'Ya existe un rol con ese nombre.',
        'name.max' => 'El nombre no puede superar 50 caracteres.',
    ];

    public function mount(): void
    {
        abort_unless(auth()->user()->can('roles.view'), 403);
    }

    public function openCreate(): void
    {
        abort_unless(auth()->user()->can('roles.create'), 403);

        $this->selected_id = null;
        $this->name = '';
        $this->selectedPerms = [];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        abort_unless(auth()->user()->can('roles.view'), 403);

        $role = Role::with('permissions')->findOrFail($id);

        $this->selected_id = $role->id;
        $this->name = $role->name;
        $this->selectedPerms = $role->permissions->pluck('name')->toArray();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        if ($this->selected_id) {
            abort_unless(auth()->user()->can('roles.edit'), 403);
        } else {
            abort_unless(auth()->user()->can('roles.create'), 403);
        }

        $this->validate();

        if ($this->selected_id) {
            $role = Role::findOrFail($this->selected_id);

            if (! in_array($role->name, self::PROTECTED_ROLES)) {
                $role->update(['name' => $this->name]);
            }

            if ($role->name !== 'superAdmin' && auth()->user()->can('permissions.assign')) {
                $role->syncPermissions($this->selectedPerms);
            }

            LivewireAlert::title('Rol actualizado')
                ->text('Los cambios del rol se guardaron correctamente.')
                ->success()
                ->asToast()
                ->show();
        } else {
            $role = Role::create(['name' => $this->name, 'guard_name' => 'web']);

            if (auth()->user()->can('permissions.assign')) {
                $role->syncPermissions($this->selectedPerms);
            }

            LivewireAlert::title('Rol creado')
                ->text('El nuevo rol fue registrado correctamente.')
                ->success()
                ->asToast()
                ->show();
        }

        $this->closeModal();
    }

    public function confirmDelete(int $id): void
    {
        abort_unless(auth()->user()->can('roles.delete'), 403);

        $this->deleteId = $id;

        $role = Role::withCount('users')->findOrFail($id);

        LivewireAlert::title('Confirmar eliminación')
            ->text("¿Seguro que deseas eliminar el rol «{$role->name}»? Esta acción no se puede deshacer.")
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
            ->onConfirm('deleteConfirmed')
            ->show();
    }

    public function deleteConfirmed(): void
    {
        abort_unless(auth()->user()->can('roles.delete'), 403);

        $role = Role::withCount('users')->findOrFail($this->deleteId);

        if (in_array($role->name, self::PROTECTED_ROLES)) {
            LivewireAlert::title('Rol protegido')
                ->text('Este rol del sistema no puede eliminarse.')
                ->error()
                ->asToast()
                ->show();

            return;
        }

        if ($role->users_count > 0) {
            LivewireAlert::title('Rol en uso')
                ->text("Este rol tiene {$role->users_count} usuario(s) asignado(s).")
                ->warning()
                ->asToast()
                ->show();

            return;
        }

        $role->delete();

        LivewireAlert::title('Rol eliminado')
            ->text('El rol fue eliminado correctamente.')
            ->success()
            ->asToast()
            ->show();

        $this->deleteId = null;
    }

    public function toggleExpand(int $id): void
    {
        $this->expandedRole = $this->expandedRole === $id ? null : $id;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->selected_id = null;
        $this->name = '';
        $this->selectedPerms = [];
        $this->resetValidation();
    }

    public function render()
    {
        $roles = Role::withCount(['permissions', 'users'])
            ->with('permissions')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderBy('id')
            ->get();

        $modules = config('permissions.modules', []);
        $totalPerms = Permission::count();
        $allPermissions = Permission::orderBy('name')->get();

        return view('livewire.admin.roles.index', compact('roles', 'modules', 'totalPerms', 'allPermissions'));
    }
}
