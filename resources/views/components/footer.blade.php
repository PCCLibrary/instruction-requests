<footer class="bg-gray-600 p-0 mt-4 dark:bg-gray-600">
    <div class="w-full max-w-screen-xl mx-auto py-4 md:py-8">
        <div class="text-sm text-gray-100 text-center dark:text-gray-100">
            <div class="mb-2">
                <x-heroicon-o-document-text class="inline w-4 h-4 mr-1" />
                <a class="underline hover:text-white" href="https://docs.google.com/document/d/1AgxpgeGckkmmjQIhPy-2eLhVfY-gxvEYULZwGdW8JwQ/edit?usp=sharing" target="_blank">Documentation</a> •
                <x-heroicon-o-exclamation-triangle class="inline w-4 h-4 mr-1 ml-2" />
                <a class="underline hover:text-white" href="https://forms.gle/spH4ueJxhjv8LTD17" target="_blank">Report an issue</a> •
                <x-heroicon-o-clipboard-document-list class="inline w-4 h-4 mr-1 ml-2" />
                <a class="underline hover:text-white" href="{{ config('app.public_form_url') }}" target="_blank">Public form</a>
            </div>
            <div>
                © {{ date('Y') }} PCC Library
            </div>
        </div>
    </div>
</footer>
