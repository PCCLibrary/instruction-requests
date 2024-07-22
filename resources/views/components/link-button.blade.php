@props(['url'])

<a href="{{ $url }}" style="display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007bff; border-radius: 5px; text-decoration: none;">
    {{ $slot }}
</a>
