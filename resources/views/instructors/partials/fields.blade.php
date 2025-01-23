<!-- resources/views/instructors/partials/fields.blade.php -->
<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
    <div>
        <x-input-text name="name" label="Name"
                      value="{{ old('name', $instructor?->name ?? '') }}"
                      required />
    </div>

    <div>
        <x-input-text name="display_name" label="Display Name"
                      value="{{ old('display_name', $instructor?->display_name ?? '') }}"
                      required />
    </div>

    <div>
        <x-input-text name="pronouns" label="Pronouns"
                      value="{{ old('pronouns', $instructor?->pronouns ?? '') }}" />
    </div>

    <div>
        <x-input-text name="email" label="Email"
                      value="{{ old('email', $instructor?->email ?? '') }}"
                      required />
    </div>

    <div>
        <x-input-tel name="phone" label="Phone"
                     value="{{ old('phone', $instructor?->phone ?? '') }}"
                     helptext="Format: (999) 999-9999" />
    </div>

</div>
