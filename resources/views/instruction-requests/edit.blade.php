@extends('layouts.app')

@section('content')
    {{-- Remove the extra container since app.blade.php already provides one --}}
    @if($instructionRequest->status === 'assigned' && $instructionRequest->detail->assigned_librarian_id === auth()->id())
        @include('instruction-requests.partials.accept')
    @endif

    <form action="{{ route('instructionRequests.update', $instructionRequest->id) }}" method="POST">
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Left Column (4 columns) --}}
            <div class="col-span-12 md:col-span-4 space-y-6">
                <x-card title="Manage" class="bg-sky-50">
                    @include('instruction-requests.partials.editor')
                    @include('instruction-requests.partials.fields')
                </x-card>

                <x-card title="File Attachments" class="bg-blue-50">
                    @include('instruction-requests.partials.file_attachments')
                </x-card>
            </div>

            {{-- Right Column (8 columns) --}}
            <div class="col-span-12 md:col-span-8 space-y-6">
                @include('instruction-requests.partials.admin_view')

                <x-card title="Notes" class="bg-green-50">
                    @include('instruction-requests.partials.notes')
                </x-card>
            </div>
        </div>
    </form>

    <div class="mt-6">
        <x-card title="Comments" class="bg-sky-50">
{{--            <x-comments::index :model="$instructionRequest" />--}}
        </x-card>
    </div>
@endsection

@push('third_party_scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('editToggle', () => ({
                isEditing: false,
                toggleEdit() {
                    this.isEditing = !this.isEditing
                    document.querySelectorAll('.edit-field input, .edit-field select').forEach(el => {
                        el.disabled = !this.editing
                    })
                }
            }))
        })
    </script>
@endpush
