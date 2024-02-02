<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Instruction Requests</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
<div class="container mt-5">
    @if(session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" role="alert">
            {{ session('error') }}
        </div>
    @endif

    <h2>Instruction Request Form</h2>
    <form action="{{ route('public.instructions.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="instructor_name" class="form-label">Instructor Name</label>
            <input type="text" class="form-control" id="instructor_name" name="instructor_name" required>
        </div>
        <div class="mb-3">
            <label for="display_name" class="form-label">Display Name</label>
            <input type="text" class="form-control" id="display_name" name="display_name">
        </div>
        <div class="mb-3">
            <label for="instructor_email" class="form-label">Instructor Email</label>
            <input type="email" class="form-control" id="instructor_email" name="instructor_email" required>
        </div>
        <div class="mb-3">
            <label for="instructor_phone" class="form-label">Instructor Phone</label>
            <input type="tel" class="form-control" id="instructor_phone" name="instructor_phone">
        </div>
        <div class="mb-3">
            <label for="instruction_type" class="form-label">Instruction Type</label>
            <select class="form-select" id="instruction_type" name="instruction_type" required>
                <option value="">Choose...</option>
                <option value="Synchronous">Synchronous</option>
                <option value="Asynchronous">Asynchronous</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="course_modality" class="form-label">Course Modality</label>
            <select class="form-select" id="course_modality" name="course_modality" required>
                <option value="">Choose...</option>
                <option value="Online">Online</option>
                <option value="In-person">In-person</option>
                <option value="Hybrid">Hybrid</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="librarian" class="form-label">Librarian Preference</label>
            <select class="form-select" id="librarian" name="librarian">
                <option value="">No Preference</option>
                @foreach($librarians as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="class_location" class="form-label">Class Location</label>
            <select class="form-select" id="class_location" name="class_location">
                <option value="">Select Location</option>
                @foreach($locations as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="course_department" class="form-label">Course Department</label>
            <input type="text" class="form-control" id="course_department" name="course_department" required>
        </div>
        <div class="mb-3">
            <label for="course_number" class="form-label">Course Number</label>
            <input type="text" class="form-control" id="course_number" name="course_number" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
<!-- Bootstrap Bundle with Popper -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
