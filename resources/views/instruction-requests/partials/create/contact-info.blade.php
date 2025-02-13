{{-- resources/views/instruction-requests/partials/contact-info.blade.php --}}
<x-card title="Contact Information" class="bg-white mb-4">
    <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-1">
                    <x-input-text
                        name="name"
                        label="Name"
                        :value="old('name')"
                        required
                        help-text="Instructor name"
                    />
                </div>

                <div class="md:col-span-1">
                    <x-input-text
                        name="display_name"
                        label="Preferred Name"
                        :value="old('display_name')"
                        help-text='"Students refer to me as"'
                    />
                </div>

                <div class="md:col-span-1">
                    <x-input-text
                        name="pronouns"
                        label="Pronouns"
                        :value="old('pronouns')"
                    />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-1">
                    <x-input-text
                        name="email"
                        label="Email"
                        type="email"
                        :value="old('email')"
                        required
                    />
                </div>

                <div class="md:col-span-1">
                    <x-input-text
                        name="phone"
                        label="Phone"
                        type="tel"
                        :value="old('phone')"
                    />
                </div>
            </div>

        </div>
    </div>
</x-card>
