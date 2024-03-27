<div class="container mt-4">
    <div class="row">

        <div class="form-group col-md-12">
            {!! Form::label('name', 'User name:') !!}
            <p>{!! $user->name !!}</p>
        </div>

        <!-- Display Name Field -->
        <div class="form-group col-md-4">
            {!! Form::label('display_name', 'Name:') !!}
            {!! Form::text('display_name', null, ['class' => 'form-control']) !!}
        </div>

        <!-- Campus ID Field -->
        <div class="form-group col-md-4">
            {!! Form::label('campus_id', 'Campus:') !!}
            {!! Form::select('campus_id', $campuses, old('campus_id'), ['class' => 'form-control', 'placeholder' => 'Select Campus']) !!}
        </div>


        <!-- Email Field -->
        <div class="form-group col-md-4">
            {!! Form::label('email', 'Email:') !!}
            {!! Form::email('email', null, ['class' => 'form-control']) !!}
        </div>

        <!-- Password Field -->
        <div class="form-group col-md-6">
            {!! Form::label('password', 'Password:') !!}
            {!! Form::password('password', ['class' => 'form-control']) !!}
        </div>

        <!-- Confirmation Password Field -->
        <div class="form-group col-md-6">
            {!! Form::label('password_confirmation', 'Password Confirmation:') !!}
            {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
        </div>

        <!-- Submit and Cancel buttons -->
        <div class="form-group col-md-12">
            {!! Form::submit('Save', ['class' => 'btn btn-primary']) !!}
            <a href="{!! route('users.index') !!}" class="btn btn-default">Cancel</a>
        </div>
    </div>
</div>
