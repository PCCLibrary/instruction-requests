{{-- Check if we are editing an existing instruction request --}}
@isset($instructionRequest->id)

        <x-row>
            <div class="col-3">

                <ul class="list-unstyled">
{{--                    <li><h3 class="mb-2">Instructor</h3></li>--}}
                    <li><strong>Contact Information</strong></li>
                    <li><a href="{{ route('instructors.edit', $instructionRequest->instructor_id) }}" title="Click to edit instructor information"><i class="fa fa-edit"></i> {{ $instructionRequest->instructor->display_name }}</a></li>
                    <li><a href="mailto:{{ $instructionRequest->instructor->email }}" title="Click to email instructor"><i class="fa fa-envelope"></i> {{ $instructionRequest->instructor->email }}</a></li>
                </ul>
            </div>

            <div class="col-4">
                <ul class="list-unstyled">
                    <li><label class="mr-4">Class syllabus</label></li>
                    @if($syllabus->isNotEmpty())
                        @foreach($syllabus as $item)
                            <li class="text-blue"><i class="fa fa-file"></i> {{ $item->file_name }} - <a href="{{ $item->getUrl() }}" target="_blank">View</a></li>
                        @endforeach
                    @else
                        <li>No attached syllabus files.</li>
                    @endif
                </ul>
            </div>

            <div class="col-4">
                <ul class="list-unstyled">
                    <li><label>Class Assignments</label></li>
                    @if($instructorAttachments->isNotEmpty())
                        @foreach($instructorAttachments as $item)
                            <li class="text-blue"><i class="fa fa-file"></i> {{ $item->file_name }} - <a href="{{ $item->getUrl() }}" target="_blank">Download</a></li>
                        @endforeach
                    @else
                        <li>No attached assignment files.</li>
                    @endif
                </ul>

            </div>

        </x-row>

        @if(isset($instructionRequest->assignment_description))
            <x-row classes="row py-0 my-0">

                <div class="col-12">
                    <b>Assignment Description</b>
                    <p>{{ $instructionRequest->assignment_description }}</p>
                </div>


            </x-row>
        @endif

    {!! Form::hidden('instruction_request_id', $instructionRequest->id) !!}
    {!! Form::hidden('instructor_id', $instructionRequest->instructor_id) !!}

@else
    <x-row>
        <h3 class="col-12 mb-4">Contact Information</h3>
        {{-- Instructor Name --}}
        <x-input-text name="name"
                      label="Name"
                      :value="old('name')"
                      helptext="Instructor name"
                      classes="col-6"
                      required=true
        />

        {{-- Display Name --}}
        <x-input-text name="display_name"
                      label="Display Name"
                      :value="old('display_name')"
                      helptext='"Students refer to me as"'
                      classes="col-6"
        />

        {{-- Pronouns --}}
        <x-input-text name="pronouns"
                      label="Pronouns"
                      :value="old('pronouns')"
                      classes="col-4"
            {{--                      helptext='Preferred pronouns'--}}

        />

        {{-- Email --}}
        <x-input-text name="email"
                      label="Email"
                      :value="old('email')"
                      classes="col-4"
        />

        {{-- Phone --}}
        <x-input-text name="phone"
                      label="Phone"
                      :value="old('phone')"
                      classes="col-4"
        />
    </x-row>
@endisset
