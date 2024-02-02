@extends('layouts.app')

@section('content')
    <section class="content-header w-100 d-flex">
        <div class="d-flex w-100 p-2 align-content-center">
            <h1 class="col">Librarians</h1>
            <div class="col">
               <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('users.create') !!}">Add New</a>
            </div>
        </div>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('users.table')
            </div>
        </div>
        <div class="text-center">

        </div>
    </div>
@endsection

