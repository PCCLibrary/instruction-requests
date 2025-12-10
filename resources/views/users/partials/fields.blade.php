<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
    <div>
        <x-input-label for="name" :value="__('User Name')" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name ?? '')" required autocomplete="name" />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="display_name" :value="__('Display Name')" />
        <x-text-input id="display_name" name="display_name" type="text" class="mt-1 block w-full" :value="old('display_name', $user->display_name ?? '')" required autocomplete="display_name" />
        <x-input-error class="mt-2" :messages="$errors->get('display_name')" />
    </div>

    <div>
        <x-input-label for="email" :value="__('Email')" />
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email ?? '')" required autocomplete="email" />
        <x-input-error class="mt-2" :messages="$errors->get('email')" />
    </div>

    <div>
        <x-multiselect
            name="campus_ids"
            label="Campuses"
            placeholder="Select campuses..."
            :options="$campuses"
            :selected="old('campus_ids', isset($user) ? $user->campuses->pluck('id')->toArray() : [])"
            searchable
        />
        <x-input-error class="mt-2" :messages="$errors->get('campus_ids')" />
        <x-input-error class="mt-2" :messages="$errors->get('campus_ids.*')" />
    </div>

    <div>
        <x-input-checkbox id="is_scheduler"
                          name="is_scheduler"
                          label="Is Scheduler"
                          class="mt-1 block w-full"
                          :checked="old('is_scheduler', $user->is_scheduler ?? false)" />
    </div>

</div>
