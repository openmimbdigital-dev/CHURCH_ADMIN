<?php

namespace App\Livewire\Concerns;

trait FormatsDatatableActionsColumn
{
    public function cellClasses($row, $column): string
    {
        $base = 'text-sm text-gray-900 px-3 py-2 sm:px-5 whitespace-nowrap';

        $columnName = is_array($column)
            ? ($column['name'] ?? null)
            : ($column->name ?? null);

        if ($columnName === 'actions') {
            return $base.' w-px';
        }

        return $base;
    }
}
