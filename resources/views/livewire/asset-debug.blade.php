<div>
    <h3>Asset Debug Information</h3>

    <div class="debug-section">
        <h4>Environment</h4>
        <ul>
            <li>Base URL: {{ $baseUrl }}</li>
            <li>Asset URL: {{ $assetUrl }}</li>
            <li>App URL: {{ $appUrl }}</li>
            <li>Public Path: {{ $publicPath }}</li>
        </ul>
    </div>

    <div class="debug-section">
        <h4>Livewire Configuration</h4>
        <pre>{{ print_r($livewireConfig, true) }}</pre>
    </div>

    <div class="debug-section">
        <h4>Livewire Routes</h4>
        <pre>{{ print_r($routes->toArray(), true) }}</pre>
    </div>

    <div class="debug-section">
        <h4>Request Headers</h4>
        <pre>{{ print_r($headers, true) }}</pre>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const debugInfo = {
                livewireLoaded: typeof window.Livewire !== 'undefined',
                alpineLoaded: typeof window.Alpine !== 'undefined',
                currentScript: document.currentScript?.src,
                baseURI: document.baseURI,
                scriptElements: Array.from(document.getElementsByTagName('script')).map(s => s.src),
                linkElements: Array.from(document.getElementsByTagName('link')).map(l => l.href)
            };

            console.log('Client Debug Info:', debugInfo);
        });
    </script>
</div>
