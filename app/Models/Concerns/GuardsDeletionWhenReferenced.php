<?php

namespace App\Models\Concerns;

trait GuardsDeletionWhenReferenced
{
    public function canBeDeleted(): bool
    {
        return blank($this->deletionBlockedReason());
    }

    public function deletionBlockedReason(): ?string
    {
        foreach ($this->deletionBlockingRelations() as $relation => $label) {
            if (method_exists($this, $relation) && $this->{$relation}()->exists()) {
                return "No se puede eliminar: tiene {$label} asociados.";
            }
        }

        return null;
    }

    /**
     * @return array<string, string> relationMethod => etiqueta humana (plural)
     */
    protected function deletionBlockingRelations(): array
    {
        return [];
    }
}
