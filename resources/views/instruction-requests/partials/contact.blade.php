{{-- Check if we are editing an existing instruction request --}}
@isset($instructionRequest->id)
    <div class="grid grid-cols-12 gap-6">
        {{-- Contact Information Section --}}
        <div class="col-span-3">
            <ul class="space-y-2">
                <li><span class="font-semibold">Contact Information</span></li>
                <li>
                    <a href="{{ route('instructors.edit', $instructionRequest->instructor_id) }}"
                       class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        {{ $instructionRequest->instructor->display_name }}
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ $instructionRequest->instructor->email }}"
                       class="text-blue-600 hover:text-blue-800 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                        </svg>
                        {{ $instructionRequest->instructor->email }}
                    </a>
                </li>
            </ul>
        </div>

        {{-- Syllabus Section --}}
        <div class="col-span-4">
            <ul class="space-y-2">
                <li><span class="font-semibold">Class syllabus</span></li>
                @if($syllabus->isNotEmpty())
                    @foreach($syllabus as $item)
                        <li class="flex items-center gap-2 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                            {{ $item->file_name }} -
                            <a href="{{ $item->getUrl() }}"
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800">
                                View
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="text-gray-600">No attached syllabus files.</li>
                @endif
            </ul>
        </div>

        {{-- Assignments Section --}}
        <div class="col-span-4">
            <ul class="space-y-2">
                <li><span class="font-semibold">Class Assignments</span></li>
                @if($instructorAttachments->isNotEmpty())
                    @foreach($instructorAttachments as $item)
                        <li class="flex items-center gap-2 text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                            </svg>
                            {{ $item->file_name }} -
                            <a href="{{ $item->getUrl() }}"
                               target="_blank"
                               class="text-blue-600 hover:text-blue-800">
                                Download
                            </a>
                        </li>
                    @endforeach
                @else
                    <li class="text-gray-600">No attached assignment files.</li>
                @endif
            </ul>
        </div>
    </div>

    {{-- Assignment Description --}}
    @if(isset($instructionRequest->assignment_description))
        <div class="mt-6">
            <span class="font-semibold">Assignment Description</span>
            <p class="mt-2 text-gray-700">{{ $instructionRequest->assignment_description }}</p>
        </div>
    @endif

    <input type="hidden" name="instruction_requests_id" value="{{ $instructionRequest->id }}">
    <input type="hidden" name="instructor_id" value="{{ $instructionRequest->instructor_id }}">

@else
    {{-- Create New Contact Form --}}
    <div class="space-y-6">
        <h3 class="text-lg font-semibold leading-6 text-gray-900">Contact Information</h3>

        <div class="grid grid-cols-12 gap-6">
            {{-- Name --}}
            <div class="col-span-6">
                <x-input-text
                    name="name"
                    label="Name"
                    :value="old('name')"
                    help-text="Instructor name"
                    required
                />
            </div>

            {{-- Display Name --}}
            <div class="col-span-6">
                <x-input-text
                    name="display_name"
                    label="Display Name"
                    :value="old('display_name')"
                    help-text='"Students refer to me as"'
                />
            </div>

            {{-- Pronouns --}}
            <div class="col-span-4">
                <x-input-text
                    name="pronouns"
                    label="Pronouns"
                    :value="old('pronouns')"
                />
            </div>

            {{-- Email --}}
            <div class="col-span-4">
                <x-input-text
                    name="email"
                    label="Email"
                    :value="old('email')"
                    type="email"
                />
            </div>

            {{-- Phone --}}
            <div class="col-span-4">
                <x-input-text
                    name="phone"
                    label="Phone"
                    :value="old('phone')"
                    type="tel"
                />
            </div>
        </div>
    </div>
@endisset
