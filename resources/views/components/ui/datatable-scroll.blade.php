<div {{ $attributes->merge(['class' => 'datatable-scroll min-w-0 max-w-full']) }} x-data="dualScroll">
    <div
        x-ref="top"
        x-on:scroll="syncFromTop()"
        x-show="hasHorizontalScroll"
        x-cloak
        class="datatable-scroll-top overflow-x-auto overflow-y-hidden"
        aria-hidden="true"
    >
        <div x-ref="topInner" class="datatable-scroll-top-inner h-px"></div>
    </div>

    <div
        x-ref="main"
        class="datatable-corporate datatable-scroll-main overflow-x-auto"
    >
        {{ $slot }}
    </div>
</div>
