{{-- Auto-generated breadcrumbs via View Composer --}}
@if(isset($breadcrumbs) && count($breadcrumbs) > 0)
<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        @foreach ($breadcrumbs as $index => $breadcrumb)
            <li @if($index > 0) class="inline-flex items-center" @endif>
                @if($index > 0)
                    <svg class="w-3 h-3 text-gray-400 mx-1" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                    </svg>
                @endif

                @if(isset($breadcrumb['route']))
                    <a href="{{ route($breadcrumb['route']) }}"
                       class="text-sm font-medium text-gray-600 hover:text-cyan-600 dark:text-gray-400 dark:hover:text-cyan-400">
                        {{ $breadcrumb['label'] }}
                    </a>
                @else
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $breadcrumb['label'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
@endif
