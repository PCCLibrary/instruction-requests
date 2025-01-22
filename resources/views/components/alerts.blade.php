<div class="py-6">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Success</p>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Error</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Info</p>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @if (session('warning'))
            <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Warning</p>
                <p>{{ session('warning') }}</p>
            </div>
        @endif

    </div>
</div>
