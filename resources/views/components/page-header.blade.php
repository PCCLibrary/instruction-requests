<!-- resources/views/components/page-header.blade.php -->

@props(['title', 'text'])

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1>{{ $title }}</h1>
                <p>{{ html_entity_decode($text) }}</p>
            </div>
        </div>
    </div>
</section>
