{{-- resources/views/instruction-requests/partials/create/contact-info.blade.php --}}
<x-fieldset legend="Contact Information" classes="bg-white">
    <div class="space-y-6">
        {{-- Name and Display Name Row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-input-text
                name="name"
                id="name"
                label="Instructor Name"
                :value="old('name')"
                help-text="Your full name"
                required
            />

            <x-input-text
                name="display_name"
                id="display_name"
                label="Students refer to me as"
                :value="old('display_name')"
                help-text="How you would like students to address you"
            />
        </div>

        {{-- Pronouns, Email, Phone Row --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-input-text
                name="pronouns"
                id="pronouns"
                label="Pronouns"
                :value="old('pronouns')"
            />

            <x-input-text
                name="email"
                id="email"
                label="Email"
                type="email"
                :value="old('email')"
                required
            />

            <x-input-text
                name="phone"
                id="phone"
                label="Phone"
                type="tel"
                :value="old('phone')"
            />
        </div>
    </div>
</x-fieldset>
