@extends('layouts.app')

@section('header')
    <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:text-3xl sm:truncate">
        Dropzone Component Test
    </h1>
@endsection

@section('content')
    <div class="container mx-auto py-6">
        <div class="bg-white shadow-md rounded-lg p-6 max-w-3xl mx-auto">
            <h2 class="text-xl font-semibold mb-4">Test File Upload with Dropzone</h2>

            <form action="{{ route('public.instruction-request.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="test-dropzone" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Files
                    </label>

                    <x-dropzone
                        id="test-dropzone"
                        collection="materials"
                        :maxFiles="4"
                        :maxFileSize="20"
                        tokenFieldName="upload_token"
                    />

                    <p class="mt-2 text-sm text-gray-500">
                        This is a test implementation of the Dropzone component. Files uploaded here can be submitted using the form button below.
                    </p>
                </div>

                <div class="mt-6">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Submit Test Form
                    </button>
                </div>
            </form>

            <div class="mt-8">
                <h3 class="text-lg font-medium mb-2">Component Information:</h3>
                <div class="bg-gray-100 p-4 rounded">
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>Component uses token-based secure uploads</li>
                        <li>Files are temporarily stored until form submission</li>
                        <li>Supported file types: PDF, Word, PowerPoint, and text files</li>
                        <li>Maximum 4 files, 20MB each</li>
                        <li>Drag and drop interface with progress indicators</li>
                        <li>Automatic file validation with error messages</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
