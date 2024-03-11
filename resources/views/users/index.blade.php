@extends('layouts.app')

@section('content')
    <x-page-header title="Librarians" text="Manage librarian user accounts">
           <a class="btn btn-success float-right"
              href="{!! route('users.create') !!}">Add New</a>
    </x-page-header>

    <div class="content px-3">

        @include('flash::message')

        <div class="clearfix"></div>

        <div class="card">
            <div class="card-body p-0">
                @include('users.table')
            </div>

        </div>
    </div>
@endsection

