    {{-- resources/views/components/page-header.blade.php --}}
@props(['title', 'text'])

<section>
    <div class="container mx-auto px-4 py-6">
        <div class="flex flex-col md:flex-row md:items-start md:justify-between">
            <div class="flex-1 min-w-0 mb-4 md:mb-0 md:pr-4">
                <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
                    {{ $title }}
                </h1>
                @if($text)
                    <p class="mt-2 text-base text-gray-600 dark:text-gray-300">
                        {{ html_entity_decode($text) }}
                    </p>
                @endif
            </div>

            <div class="flex-shrink-0 flex items-center justify-start md:justify-end">
                {{ $slot }}
            </div>
        </div>
    </div>
</section>
