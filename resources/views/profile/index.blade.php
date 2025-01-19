@extends('layouts.app')

@section('content')
    <x-page-header title="My Profile" text="Manage your account settings">
        {{-- No Add New button needed for profile --}}
    </x-page-header>

    <div class="content px-3">
        <div class="clearfix"></div>

        <div class="card space-y-6">
            <div class="card-body p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.edit-profile-form')
                </div>
            </div>

            <div class="card-body p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card-body p-4 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
