<nav aria-label="Breadcrumb" class="flex min-w-0 items-center gap-2 overflow-x-auto whitespace-nowrap text-xs font-semibold text-slate-500">
    @foreach ($items as $item)
        @if (! $loop->first)
            <svg class="h-3.5 w-3.5 shrink-0 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m9 18 6-6-6-6" />
            </svg>
        @endif

        @if ($item['route'])
            <a href="{{ route($item['route'], $item['parameters']) }}" class="rounded px-1 py-0.5 transition-colors hover:bg-blue-50 hover:text-blue-700">
                {{ $item['label'] }}
            </a>
        @else
            <span class="{{ $loop->last ? 'text-slate-700' : '' }}">{{ $item['label'] }}</span>
        @endif
    @endforeach
</nav>
