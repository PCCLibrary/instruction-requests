@extends('layouts.public') {{-- Assuming your layout file is named public.blade.php --}}

@section('content')

    {{-- Container and row to center the card horizontally --}}
    <div class="container mt-4">
        <div class="row justify-content-center">
            {{-- Column to control the card width (6 columns on medium screens and up) --}}
            <div class="col-md-6">

                {{-- Bootstrap Card structure --}}
                <div class="card no-border">

                    {{-- Card Header with styling for title --}}
                    {{-- Using p-4 for padding, bg-primary for background, text-white for text, and text-center for centering --}}
                    <div class="card-header p-4 bg-primary text-white text-center">
                        {{-- Use a heading tag for the title --}}
                        <h1 class="h4 card-title mb-0">PCC Library Instruction Request Dashboard</h1>
                    </div>

                    {{-- Card Body for main content --}}
                    {{-- Using text-center to center content within the body --}}
                    <div class="card-body text-center">
                        {{-- Descriptive text --}}
                        {{-- mb-4 adds margin below the paragraph --}}
                        {{-- text-gray-600 class might come from a CSS file included in your layout, or you can use Bootstrap's text-secondary etc. --}}
                        <p class="mb-4 text-gray-600">Welcome to the PCC Library Instruction Request System.<br/>Please log in with your PCC credentials to access the system.</p>

                        {{-- Login Button --}}
                        {{-- Using Bootstrap classes and the inline style for the specific blue color --}}
                        <a href="{{ route('saml2.login') }}"
                           class="btn btn-primary btn-lg"
                           style="background-color: #008099; border-color: #008099; color: white;">
                            Librarian Login
                        </a>

                        <hr class="h5 my-4 w-100"/>

                        {{-- Logout Button --}}
                        {{-- Using Bootstrap classes and the inline style for the specific blue color --}}

                        {{-- New Link for Instruction Request Form wrapped in Bootstrap info alert with icon and dismiss --}}
                        <div class="alert alert-info d-flex align-items-center text-left fade show mt-3" role="alert">
                            {{-- Font Awesome 4 Info Icon --}}
                            {{-- Ensure you have Font Awesome 4 CSS included in your layout --}}
                            <i class="fa fa-2x fa-info-circle mr-4"></i> {{-- Added mr-2 for spacing between icon and text --}}
                            {{-- Alert text and link --}}
                            <a href="/library/instruction-request/" class="alert-link">If you want to create a new Instruction Request, click here to access the Instruction Request Form.</a>
                        </div>


                    </div>

                    {{-- Card Footer for supplementary information --}}
                    {{-- Using text-center for centering and text-muted for muted text color --}}
                    {{-- small tag is used for smaller text size --}}
                    <div class="card-footer text-center text-muted small">
                        <p class="mb-1">Need help? <a href="library-dst-group@pcc.edu">Contact DST</a></p>
                        <p class="mb-0">Version 1.0 &copy; 2025 Portland Community College</p>
                    </div>

                </div> {{-- End Bootstrap Card --}}

            </div> {{-- End column --}}
        </div> {{-- End row --}}
    </div> {{-- End container --}}

@endsection
