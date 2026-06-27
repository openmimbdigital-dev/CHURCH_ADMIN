<?php

namespace App\View\Composers;

use App\Models\MenuSection;
use App\Models\User;
use Illuminate\View\View;

class SidebarMenuComposer
{
    public function compose(View $view): void
    {
        /** @var User|null $user */
        $user = auth()->user();

        $sections = MenuSection::query()
            ->where('is_active', true)
            ->with(['activeItems'])
            ->orderBy('sort_order')
            ->get()
            ->map(function (MenuSection $section) use ($user) {
                $visibleItems = $section->activeItems
                    ->filter(fn ($item) => $item->isVisibleTo($user))
                    ->values();

                $section->setRelation('activeItems', $visibleItems);

                return $section;
            })
            ->filter(fn (MenuSection $section) => $section->activeItems->isNotEmpty())
            ->values();

        $view->with('menuSections', $sections);
    }
}
