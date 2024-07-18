@extends('emails.master')

@section('title', 'Instruction Request Confirmation')

@section('heading', 'Your Instruction Request Has Been Received')

@section('intro')
    Dear {{ $instructor_name }},
    Thank you for submitting your instruction request. Here are the details of your request:
@endsection

@section('content')
    <p>Our team will review your request and get back to you shortly.</p>
    <p>Regards,<br>PCC Librarians</p>
@endsection
