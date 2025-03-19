// Disable Dropzone auto-discovery immediately
if (typeof Dropzone !== 'undefined') {
    Dropzone.autoDiscover = false;
}

$(document).ready(function() {
    /**
     * Hide all fieldsets and destroy Select2 instances if initialized.
     */
    function hideAllFieldsets() {
        $('.on-campus, .remote, .asynchronous').addClass('d-none').each(function() {
            if ($(this).find('select').data('select2')) {
                $(this).find('select').select2('destroy');
            }
        });
    }

    /**
     * Apply field settings based on the selected instruction type.
     * @param {string} value - The selected instruction type value.
     */
    function applyFieldSettings(value) {
        hideAllFieldsets(); // Hide all fieldsets first.

        if (value && value in fieldSettings) {
            const settings = fieldSettings[value];

            // Show the relevant fieldset
            $(settings.show).removeClass('d-none').find('select').select2({
                theme: 'bootstrap4',
                width: '100%'
            });

            // Enable all fields before applying specific disabled settings
            $('.on-campus, .remote, .asynchronous').find('input, select, textarea').prop('disabled', false);

            // Set fields as required
            $.each(settings.required, function(index, selector) {
                $(selector).prop('required', true);
                $(`label[for="${selector.substring(1)}"]`).addClass('is-required');
            });

            // Set fields as not required
            $.each(settings.notRequired, function(index, selector) {
                $(selector).prop('required', false);
                $(`label[for="${selector.substring(1)}"]`).removeClass('is-required');
            });

            // Disable specific fields
            $.each(settings.disable, function(index, selector) {
                $(selector).prop('disabled', true);
            });
        }
    }

    const fieldSettings = {
        'on-campus': {
            show: '.on-campus',
            required: ['#number_of_students', '#campus_id', '#preferred_datetime', '#duration'],
            notRequired: ["#librarian_id", "#asynchronous_instruction_ready_date", "#alternate_datetime"],
            disable: ["#asynchronous_instruction_ready_date"]
        },
        'remote': {
            show: '.remote',
            required: ['#number_of_students', '#librarian_id', '#preferred_datetime', '#duration', '#campus_id'],
            notRequired: ["#asynchronous_instruction_ready_date", "#alternate_datetime"],
            disable: ["#asynchronous_instruction_ready_date"]
        },
        'asynchronous': {
            show: '.asynchronous',
            required: ['#number_of_students', '#librarian_id', '#asynchronous_instruction_ready_date', '#campus_id'],
            notRequired: ['#preferred_datetime', '#alternate_datetime', '#duration'],
            disable: [ '#preferred_datetime', '#alternate_datetime', '#duration']
        }
    };

    // Initialize select elements
    $('#librarian_id, #department').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: function() {
            return $(this).data('placeholder');
        }
    });

    let instructionTypeSelect = $('select[name="instruction_type"]');
    applyFieldSettings(instructionTypeSelect.val()); // Apply initial settings

    // Checkbox handler
    $('input[type="checkbox"]').on('change', function() {
        const descriptionId = $(this).data('target');
        $('#' + descriptionId).toggleClass('invisible', !this.checked);
    });

    instructionTypeSelect.change(function() {
        applyFieldSettings($(this).val());
    });

    // ************************
    // Dropzone Implementation
    // ************************
    
    /**
     * Get file icon based on file extension
     * @param {string} fileName - The file name
     * @returns {string} - Font Awesome icon class
     */
    function getFileIcon(fileName) {
        const extension = fileName.split('.').pop().toLowerCase();
        const iconMap = {
            'pdf': 'fa-file-pdf-o',
            'doc': 'fa-file-word-o',
            'docx': 'fa-file-word-o',
            'ppt': 'fa-file-powerpoint-o',
            'pptx': 'fa-file-powerpoint-o',
            'txt': 'fa-file-text-o',
            'rtf': 'fa-file-text-o'
        };
        
        return iconMap[extension] || 'fa-file-o';
    }
    
    /**
     * Get CSRF token from meta tag
     * @returns {string|null}
     */
    function getCsrfToken() {
        return $('meta[name="csrf-token"]').attr('content') || null;
    }

    /**
     * Generate a token for uploading files
     * @returns {Promise<string>} - Upload token
     */
    function generateUploadToken() {
        console.log('Generating upload token with URL:', baseUrl + '/api/token/generate');
        
        const headers = {};
        const csrfToken = getCsrfToken();
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
            console.log('CSRF token found:', csrfToken);
        } else {
            console.warn('No CSRF token found in meta tag');
        }
        
        return $.ajax({
            url: baseUrl + '/api/token/generate',
            method: 'POST',
            headers: headers
        }).then(response => {
            console.log('Token generation successful:', response);
            return response;
        }).catch(error => {
            console.error('Token generation failed:', error);
            throw error;
        });
    }
    
    // Get an upload token when the page loads
    // Initialize Dropzone with the token
    if ($('#dropzone-upload').length > 0 && $('#dropzone-preview-template').length > 0) {
        console.log('Dropzone elements found, initializing upload process');
        
        // Debug the DOM elements to ensure they exist as expected
        console.log('Dropzone DOM elements:', {
            dropzoneUpload: document.getElementById('dropzone-upload'),
            dropzonePreviewTemplate: document.getElementById('dropzone-preview-template'),
            uploadToken: document.getElementById('upload-token')
        });
        
        // Check if baseUrl is defined
        console.log('Base URL:', typeof baseUrl !== 'undefined' ? baseUrl : 'undefined');
        
        // Define fallback baseUrl if not set
        if (typeof baseUrl === 'undefined') {
            baseUrl = window.location.origin;
            console.log('Using fallback baseUrl:', baseUrl);
        }
        
        generateUploadToken()
            .then(response => {
                if (response && response.token) {
                    console.log('Token received and saved:', response.token);
                    $('#upload-token').val(response.token);

                    // Initialize Dropzone with the token
                    initializeDropzone(response.token);

                    // Set a timer to warn about token expiration
                    setTimeout(function() {
                        alert('Your upload session is about to expire. Please submit the form or refresh the page to continue uploading files.');
                    }, 110 * 60 * 1000); // 110 minutes (just before the 2-hour token expires)
                } else {
                    console.error('Invalid token response:', response);
                }
            })
            .catch(error => {
                console.error('Failed to generate upload token:', error);
                // Display error message to user
                $('#dropzone-upload').prepend('<div class="alert alert-danger">Failed to initialize file upload system. Please try again or contact support.</div>');
            });
    } else {
        console.log('Dropzone elements not found, skipping initialization');
        console.log('Elements check:', {
            dropzoneUpload: $('#dropzone-upload').length,
            dropzonePreviewTemplate: $('#dropzone-preview-template').length,
            uploadToken: $('#upload-token').length
        });
    }
    
    /**
     * Initialize Dropzone with the provided token
     * @param {string} token - Upload token
     */
    function initializeDropzone(token) {
        // Get preview template
        const previewTemplate = document.querySelector('#dropzone-preview-template');
        if (!previewTemplate) {
            console.error('Dropzone preview template not found');
            return;
        }
        
        console.log('Initializing Dropzone with token:', token);

        // Create a new Dropzone instance
        try {
            const myDropzone = new Dropzone('#dropzone-upload', {
                url: baseUrl + '/api/media/upload',
                paramName: 'file', // The name used for the file upload
                maxFilesize: 20, // MB
                maxFiles: 4,
                acceptedFiles: '.pdf,.doc,.docx,.ppt,.pptx,.txt,.rtf',
                addRemoveLinks: true,
                previewTemplate: previewTemplate.innerHTML,
                headers: {
                    'X-Upload-Token': token,
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                init: function() {
                    console.log('Dropzone initialized successfully');
                    
                    this.on('addedfile', function(file) {
                        console.log('File added to Dropzone:', file.name);
                    });
                    
                    this.on('sending', function(file, xhr, formData) {
                        console.log('Sending file:', file.name);
                        console.log('Headers:', xhr.requestHeaders);
                    });
                    
                    this.on('success', function(file, response) {
                        console.log('File uploaded successfully:', file.name, response);
                        // Set the file ID as a data attribute
                        $(file.previewElement).attr('data-file-id', response.file.id);
                        
                        // Update the icon based on the file type
                        const iconClass = getFileIcon(file.name);
                        $(file.previewElement).find('.fa-file').removeClass('fa-file').addClass(iconClass);
                    });
                
                    this.on('removedfile', function(file) {
                        const fileId = $(file.previewElement).attr('data-file-id');
                        console.log('File removed from Dropzone:', file.name, 'ID:', fileId);
                        
                        if (fileId) {
                            // Delete the file on the server
                            $.ajax({
                                url: baseUrl + '/api/media/delete/' + fileId,
                                method: 'DELETE',
                                headers: {
                                    'X-Upload-Token': token,
                                    'X-CSRF-TOKEN': getCsrfToken()
                                }
                            }).done(function() {
                                console.log('File deleted from server:', file.name, 'ID:', fileId);
                            }).fail(function(error) {
                                console.error('Failed to delete file from server:', error);
                            });
                        }
                    });
                    
                    this.on('error', function(file, errorMessage, xhr) {
                        console.error('Error uploading file:', file.name, errorMessage);
                        if (xhr) {
                            console.error('XHR status:', xhr.status, 'Response:', xhr.responseText);
                        }
                        
                        if (typeof errorMessage === 'string') {
                            $(file.previewElement).find('[data-dz-errormessage]').text(errorMessage);
                        } else if (errorMessage.message) {
                            $(file.previewElement).find('[data-dz-errormessage]').text(errorMessage.message);
                        }
                    });
                    
                    // Clear the form button functionality
                    $('#clearForm').on('click', function() {
                        console.log('Removing all files from Dropzone');
                        myDropzone.removeAllFiles(true);
                    });
                }
            });
            
            // Store Dropzone instance in a global variable for form submission
            window.dropzoneInstance = myDropzone;
            
            // Add form submission handler to ensure token is included
            $('#instructionRequestForm').on('submit', function() {
                console.log('Form is being submitted. Token value:', $('#upload-token').val());
            });
            
        } catch (error) {
            console.error('Error initializing Dropzone:', error);
        }
    }
});
