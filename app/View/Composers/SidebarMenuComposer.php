<?php

namespace App\View\Composers;

use App\Models\MenuSection;
use Illuminate\View\View;

class SidebarMenuComposer
{
    public function compose(View $view): void
    {
        $view->with('menuSections', MenuSection::query()
            ->where('is_active', true)
            ->with(['activeItems'])
            ->orderBy('sort_order')
            ->get());
    }
}
