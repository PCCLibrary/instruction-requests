@php
if ($queueFailed > 0) {
    $class = "bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200";
    $text = "⚠️ {$queueFailed} failed" . ($queuePending > 0 ? ", {$queuePending} pending" : "");
} elseif ($queuePending > 0) {
    $class = "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200";
    $text = "⏳ {$queuePending} pending";
} else {
    $class = "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200";
    $text = "✅ Queue empty";
}
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $class }}">
{{ $text }}
</span>
