{{-- components/url-manager.blade.php --}}
@props(['name', 'label' => 'Enter Google Doc URL', 'value' => '', 'helptext' => null, 'classes' => 'col-auto', 'required' => null])

<div class="{{ $classes }}">
    @include('public_form.partials.label', ['label' => $label, 'name' => $name, 'required' => $required])

    @if($helptext)
        @include('public_form.partials.helptext', ['name' => $name, 'helptext' => $helptext])
    @endif

    <div class="input-group mb-3">  {{-- Bootstrap input group --}}
        <textarea rows="2" cols="50" id="url-textarea" class="form-control" placeholder="{{ $label }}"></textarea>
        <div class="input-group-append">
            <button type="button" id="add-link" class="btn btn-primary">Add Link</button>
        </div>
    </div>

    <ul id="url-list" class="list-group">  {{-- Bootstrap list group --}}
        @php
            $urls = json_decode($value, true) ?? [];
        @endphp
        @foreach($urls as $url)
            <li class="d-flex justify-content-between align-items-center">
                <a href="{{ $url }}" target="_blank">{{ $url }}</a>
                <button type="button" class="remove-url btn btn-danger btn-sm">X</button>
            </li>
        @endforeach
    </ul>

    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}">
</div>

<script>
    $(document).ready(function() {
        $("#add-link").click(function() {
            let url = $("#url-textarea").val();
            if (url.trim() !== "") {
                $("#url-list").append(`
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <a href="${url}" target="_blank">${url}</a>
                        <button type="button" class="remove-url btn btn-danger btn-sm">X</button>
                    </li>`);
                $("#url-textarea").val("");
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
