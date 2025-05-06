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
        <x-input-label for="password" :value="__('Password')" />
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password')" />
    </div>

    <div>
        <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
        <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
    </div>

    <div>
        <x-input-checkbox id="is_scheduler"
                          name="is_scheduler"
                          label="Is Scheduler"
                          class="mt-1 block w-full"
                          :checked="old('is_scheduler', $user->is_scheduler ?? false)" />
    </div>

</div>
