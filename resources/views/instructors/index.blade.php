@extends('layouts.app')

@section('page-title', 'Manage Instructors')
@section('page-description', 'Edit and manage instructors. New instructors can be added when filling in a Instruction Request Form.')

@section('content')

 @include('instructors.table')

@endsection
