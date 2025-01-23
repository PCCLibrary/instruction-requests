@extends('layouts.app')

@section('header')
    <x-breadcrumbs :breadcrumbs="[
            ['label' => 'Instruction Requests', 'route' => 'instructionRequests.index'],
            ['label' => 'Manage Instruction Request'] // No route for this one
        ]" />
    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl sm:truncate">
        Manage Instruction Request
    </h1>


@endsection

@section('content')
    {{-- Remove the extra container since app.blade.php already provides one --}}
    @if($instructionRequest->status === 'assigned' && $instructionRequest->detail->assigned_librarian_id === auth()->id())
        @include('instruction-requests.partials.accept')
    @endif

    <form action="{{ route('instructionRequests.update', $instructionRequest->id) }}"
          id="updateInstructionRequestForm"
          method="POST"
          enctype="multipart/form-data"
    >
        @csrf
        @method('PATCH')

        <div class="grid grid-cols-12 gap-6 items-start">
            {{-- Left Column (4 columns) --}}
            <div class="col-span-12 md:col-span-4 items-start">

                <x-card title="" class="bg-emerald-50  mb-4">
                    @include('instruction-requests.partials.save')
                </x-card>

                <x-card title="Status" class="bg-amber-50 mb-4">
                    @include('instruction-requests.partials.status')
                </x-card>

                <x-card title="File Attachments" class="bg-blue-50 mb-4">
                    @include('instruction-requests.partials.file-attachments')
                </x-card>

                <x-card title="" class="bg-blue-50 mb-4">
                    @include('instruction-requests.partials.tasks')
                </x-card>
            </div>

            {{-- Right Column (8 columns) --}}
            <div class="col-span-12 md:col-span-8 ">

                @include('instruction-requests.partials.request-details')

                <x-card title="Notes" class="bg-gray-50">
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
