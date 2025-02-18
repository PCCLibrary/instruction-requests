{{-- components/url-manager.blade.php --}}
@props(['name', 'label' => 'Enter Google Doc URL', 'value' => '', 'helptext' => null, 'classes' => 'col-auto', 'required' => null])

<div class="{{ $classes }}">
    @include('public_form.partials.label', ['label' => $label, 'name' => $name, 'required' => $required])

    @if($helptext)
        @include('public_form.partials.helptext', ['name' => $name, 'helptext' => $helptext])
    @endif

    <div class="url-input">
        <textarea rows="4" cols="50" id="url-textarea" placeholder="{{ $label }}"></textarea>
        <button type="button" id="add-link">Add Link</button>
    </div>

    <ul id="url-list">
        @php
            $urls = json_decode($value, true) ?? [];
        @endphp
        @foreach($urls as $url)
            <li><a href="{{ $url }}" target="_blank">{{ $url }}</a> <button type="button" class="remove-url">X</button></li>
        @endforeach
    </ul>

    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
</div>

<script>
    $(document).ready(function() {
        $("#add-link").click(function() {
            let url = $("#url-textarea").val();
            if (url.trim() !== "") { // Prevent adding empty URLs
                $("#url-list").append(`<li><a href="${url}" target="_blank">${url}</a> <button type="button" class="remove-url">X</button></li>`);
                $("#url-textarea").val(""); // Clear the textarea
                updateJsonValue();
            }
        });

        $("#url-list").on("click", ".remove-url", function() {
            $(this).parent().remove();
            updateJsonValue();
        });

        function updateJsonValue() {
            let urls = [];
            $("#url-list li a").each(function() {
                urls.push($(this).attr("href"));
            });
            $("#{{ $name }}").val(JSON.stringify(urls));
        }

        updateJsonValue(); // Initial update
    });
</script>
