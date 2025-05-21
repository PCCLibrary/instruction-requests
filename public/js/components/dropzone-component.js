// Dropzone component initialization
(function() {
    // Only run if dropzone elements exist on the page
    if (!document.querySelector('[data-dropzone]')) return;

    // Disable Dropzone auto-discovery to prevent automatic initialization
    Dropzone.autoDiscover = false;

    // Create instance for each dropzone on the page
    document.querySelectorAll('[data-dropzone]').forEach(function(element) {
        // Get configuration from data attributes
        const options = {
            url: element.dataset.uploadUrl,
            paramName: 'file',
            maxFilesize: parseInt(element.dataset.maxFileSize, 10) || 20,
            maxFiles: parseInt(element.dataset.maxFiles, 10) || 4,
            acceptedFiles: element.dataset.acceptedFiles,
            addRemoveLinks: true,
            previewTemplate: document.getElementById('dropzone-preview-template').innerHTML,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            init: function() {
                const dropzone = this;
                const tokenFieldId = element.dataset.tokenFieldId;
                const tokenField = document.getElementById(tokenFieldId);

                // Generate upload token when component is loaded
                fetch(element.dataset.tokenUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.token) {
                        // Store token in hidden input field
                        tokenField.value = data.token;

                        // Add token to dropzone headers
                        dropzone.options.headers['X-Upload-Token'] = data.token;
                    }
                })
                .catch(error => {
                    console.error('Error generating upload token:', error);
                    element.innerHTML = '<div class="bg-red-100 text-red-700 p-4 rounded">Error initializing file upload. Please refresh the page and try again.</div>';
                });

                // Setup event handlers (to be implemented in Phase 3)
                this.on('addedfile', function(file) {
                    // Placeholder for Phase 3 implementation
                });

                this.on('success', function(file, response) {
                    // Placeholder for Phase 3 implementation
                });

                this.on('removedfile', function(file) {
                    // Placeholder for Phase 3 implementation
                });
            }
        };

        // Initialize Dropzone
        new Dropzone(element, options);
    });
})();
