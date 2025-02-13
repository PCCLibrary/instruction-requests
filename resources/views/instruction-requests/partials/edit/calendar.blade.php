<p>Open Google Calendar in new window</p>
<ul class="mt-2 space-y-1">
    @foreach($campuses as $campus)
        <li>
            @if($campus->gcal)
                <a href="{{ $campus->gcal }}" target="_blank"
                   class="text-indigo-600 hover:text-indigo-900">{{ $campus->name }}</a>
            @else
                {{ $campus->name }}
            @endif
        </li>
    @endforeach
</ul>
