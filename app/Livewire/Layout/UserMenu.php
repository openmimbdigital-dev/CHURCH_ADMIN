<?php

namespace App\Livewire\Layout;

use App\Actions\User\SetUserCurrentChurch;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class UserMenu extends Component
{
    public function switchChurch(int $churchId): void
    {
        /** @var User $user */
        $user = auth()->user();

        $user = app(SetUserCurrentChurch::class)->handle($user, $churchId);

        auth()->setUser($user);

        $this->dispatch('church-switched');

        LivewireAlert::title('Iglesia activa actualizada')
            ->text('Tu contexto de trabajo se actualizó correctamente.')
            ->success()
            ->asToast()
            ->show();
    }

    public function render()
    {
        /** @var User $user */
        $user = auth()->user()->load([
            'churches' => fn ($query) => $query->orderBy('name'),
            'currentChurch:id,name',
        ]);

        return view('livewire.layout.user-menu', [
            'user' => $user,
            'displayName' => $user->full_name ?: $user->username ?: $user->email ?: 'Usuario',
            'displayEmail' => $user->email ?? '',
            'initials' => $this->initials($user),
        ]);
    }

    protected function initials(User $user): string
    {
        $displayName = $user->full_name ?: $user->username ?: $user->email ?: 'Usuario';

        if (preg_match('/\s/u', $displayName)) {
            $parts = preg_split('/\s+/u', trim($displayName));

            return mb_strtoupper(
                mb_substr($parts[0] ?? '', 0, 1).mb_substr($parts[count($parts) - 1] ?? '', 0, 1)
            );
        }

        return mb_strtoupper(mb_substr(preg_replace('/\s+/', '', $displayName), 0, 2));
    }
}
