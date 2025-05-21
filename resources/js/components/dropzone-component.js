// Dropzone component initialization
(function() {
    // Only run if dropzone elements exist on the page
    if (!document.querySelector('[data-dropzone]')) return;

    // Disable Dropzone auto-discovery to prevent automatic initialization
    Dropzone.autoDiscover = false;

    // Create instance for each dropzone on the page
    document.querySelectorAll('[data-dropzone]').forEach(function(element) {
        const tokenFieldId = element.dataset.tokenFieldId;
        const tokenField = document.getElementById(tokenFieldId);

        // Initialize upload status indicator
        const statusContainer = document.createElement('div');
        statusContainer.className = 'hidden dropzone-status my-2';
        element.parentNode.insertBefore(statusContainer, element.nextSibling);

        /**
         * Shows an error message in the status container
         * @param {string} message - The error message to display
         */
        function showError(message) {
            statusContainer.className = 'dropzone-status my-2 bg-red-100 text-red-700 p-3 rounded';
            statusContainer.innerHTML = message;
        }

        /**
         * Disables the dropzone when an error occurs
         */
        function disableDropzone() {
            element.classList.add('opacity-50', 'pointer-events-none');
            element.classList.remove('dz-clickable');
        }

        /**
         * Enables the dropzone when token is successfully generated
         */
        function enableDropzone() {
            element.classList.remove('opacity-50', 'pointer-events-none');
            element.classList.add('dz-clickable');
        }

        /**
         * Generates a token from the server
         * @returns {Promise<string|null>} - The generated token or null if generation failed
         */
        async function generateToken() {
            try {
                // Show loading state
                element.classList.add('opacity-50');

                const response = await fetch(element.dataset.tokenUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({})
                });

                if (!response.ok) {
                    throw new Error(`Server responded with ${response.status}: ${response.statusText}`);
                }

                const data = await response.json();

                if (!data.token) {
                    throw new Error('No token received from server');
                }

                // Store token in hidden form field for submission
                tokenField.value = data.token;

                // Enable dropzone
                enableDropzone();

                return data.token;
            } catch (error) {
                showError(`<strong>Error:</strong> Could not initialize file upload. Please refresh the page and try again. (${error.message})`);
                disableDropzone();
                return null;
            }
        }

        // Immediately attempt to generate a token
        generateToken().then(token => {
            if (!token) {
                return; // Don't initialize Dropzone if token generation fails
            }

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
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Upload-Token': token
                },
                dictDefaultMessage: "Drop files here or click to upload",
                dictFileTooBig: "File is too big ({{filesize}}MB). Max filesize: {{maxFilesize}}MB.",
                dictInvalidFileType: "You can't upload files of this type.",
                dictResponseError: "Server responded with {{statusCode}} code.",
                dictCancelUpload: "Cancel upload",
                dictUploadCanceled: "Upload canceled.",
                dictRemoveFile: "Remove file",
                dictMaxFilesExceeded: "You can only upload {{maxFiles}} files.",

                init: function() {
                    const dropzone = this;

                    // File added event handler
                    this.on('addedfile', function(file) {
                        // Set custom data on file object for later use
                        file.token = token;

                        // Clear error status if it was shown before
                        statusContainer.className = 'hidden dropzone-status';
                    });

                    // Success event handler
                    this.on('success', function(file, response) {
                        if (response && response.success && response.file) {
                            // Store the server-side file ID on the file object
                            file.serverId = response.file.id;

                            // Add file information as data attributes for easier access
                            file.previewElement.setAttribute('data-file-id', response.file.id);

                            // Show success indicator
                            file.previewElement.querySelector('.dz-success-mark').classList.remove('hidden');
                        }
                    });

                    // Error event handler
                    this.on('error', function(file, errorMessage, xhr) {
                        // Show the error mark
                        if (file.previewElement) {
                            file.previewElement.querySelector('.dz-error-mark').classList.remove('hidden');

                            // If the error message is an object (from server), extract the message
                            if (typeof errorMessage === 'object' && errorMessage.error) {
                                errorMessage = errorMessage.error;
                            }

                            // Show the error message
                            const errorElement = file.previewElement.querySelector('[data-dz-errormessage]');
                            errorElement.textContent = errorMessage;
                            errorElement.parentNode.classList.remove('hidden');
                        }
                    });

                    // Remove file event handler
                    this.on('removedfile', function(file) {
                        // If the file has a server ID, send delete request
                        if (file.serverId) {
                            const deleteUrl = element.dataset.deleteUrl.replace('__id__', file.serverId);

                            fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'X-Upload-Token': token
                                }
                            }).catch(error => {
                                console.error('Error deleting file:', error);
                            });
                        }
                    });

                    // Complete handling (all files uploaded)
                    this.on('complete', function() {
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            // All files have been processed, nothing to do here
                        }
                    });

                    // Reset the entire dropzone when needed
                    this.resetDropzone = function() {
                        this.removeAllFiles(true);
                        generateToken().then(newToken => {
                            if (newToken) {
                                this.options.headers['X-Upload-Token'] = newToken;
                            }
                        });
                    };

                    // IMPORTANT: Alpine.js Compatibility
                    // The following code ensures the Dropzone component works well with Alpine.js
                    // We don't touch Alpine.js global state or modify any Alpine properties
                    // We use standard DOM events for any interactions that might be needed

                    // If this page uses Alpine.js (check if window.Alpine exists)
                    if (typeof window.Alpine !== 'undefined') {
                        // When Alpine initializes a component that contains a dropzone
                        document.addEventListener('alpine:initialized', function() {
                            // If any Alpine component manipulates visibility of our container, refresh the dropzone
                            const container = element.closest('[x-data]');
                            if (container) {
                                // Use MutationObserver to detect class changes (like hidden/visible)
                                const observer = new MutationObserver(function(mutations) {
                                    mutations.forEach(function(mutation) {
                                        if (mutation.attributeName === 'class') {
                                            // Check if the element just became visible
                                            const isHidden = container.classList.contains('hidden');
                                            if (!isHidden && dropzone) {
                                                // Force resize calculation to ensure proper layout
                                                setTimeout(function() {
                                                    dropzone.emit('resize');
                                                }, 100);
                                            }
                                        }
                                    });
                                });

                                // Start observing the container for class changes
                                observer.observe(container, { attributes: true });
                            }
                        });

                        // Listen for Alpine model changes that might affect form state
                        element.closest('form')?.addEventListener('change', function() {
                            // If an Alpine data model changes in a way that could affect our dropzone,
                            // we might need to take action here, but most cases are covered by the observer above
                        });
                    }
                }
            };

            // Initialize Dropzone
            new Dropzone(element, options);
        });
    });
})();
