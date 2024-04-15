<!-- components/file-attachments.blade.php -->
@props(['attachments', 'title'])

@if($attachments->isNotEmpty())
    <ul class="list-unstyled">
        <li><strong>{{ $title }}</strong></li>
        @foreach($attachments as $item)
            <li class="text-blue"><i class="fa fa-file"></i> {{ $item->file_name }} - <a href="{{ $item->getUrl() }}" target="_blank">View</a></li>
        @endforeach
    </ul>
@else
    <div class="">
        <strong>No attached {{ $title }} files.</strong>
    </div>
@endif
