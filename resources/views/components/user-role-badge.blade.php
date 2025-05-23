@props(['user'])

@php
    $isScheduler = $user->is_scheduler ?? false;
    $role = $isScheduler ? 'Scheduler' : 'Librarian';
    $badgeClasses = $isScheduler
        ? 'bg-amber-500 text-amber-50 dark:bg-amber-600 dark:text-amber-50'
        : 'bg-emerald-500 text-emerald-50 dark:bg-emerald-600 dark:text-emerald-50';
@endphp

<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClasses }}">
    {{ $role }}
</span>
