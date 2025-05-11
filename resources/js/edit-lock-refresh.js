/**
 * Edit Form Lock Refresh
 *
 * Handles form lock refresh and timeout notifications for the edit form.
 */

// Configuration - easily adjust intervals here
const MIN_EXPIRY_DELAY = 60000; // Minimum expiry delay (1 minute)
const WARNING_OFFSET = 30000;   // Show warning 30 seconds before expiry
const EXPIRY_DELAY = Math.max(120000, MIN_EXPIRY_DELAY); // Ensure at least 1 minute expiry
const WARNING_DELAY = EXPIRY_DELAY - WARNING_OFFSET;     // Calculated warning time

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on an edit form page with lock functionality
    const editForm = document.querySelector('form.edit-form[data-request-id]');
    if (!editForm) return;

    // Check if Toaster is available to show notifications
    if (!window.Toaster) {
        console.error('Toaster is not initialized! Toast notifications may not work properly.');
    }

    const requestId = editForm.getAttribute('data-request-id');
    let warningTimeout = null;
    let expiryTimeout = null;
    let warningShown = false;

    // Store reference to warning toast
    let warningToastInstance = null;

    // Function to show a toast message
    const showToast = function(type, message, isPersistent = false) {
        // Check if Toaster library is available
        if (window.Toaster) {
            // If this is a warning toast about session expiry, make it persistent
            if (type === 'warning' && message.includes('expire') && isPersistent) {
                // If we have a previous warning toast, dismiss it first
                if (warningToastInstance && typeof warningToastInstance.dismiss === 'function') {
                    warningToastInstance.dismiss();
                }

                // Create persistent toast with longer duration
                warningToastInstance = window.Toaster.warning(message, {
                    duration: 60000, // Extra long duration for warning
                    position: 'top-center', // More noticeable position
                });
                console.log(`Persistent toast dispatched: ${type} - ${message}`);
            } else {
                // Regular toast handling
                switch(type) {
                    case 'success':
                        window.Toaster.success(message);
                        break;
                    case 'warning':
                        window.Toaster.warning(message);
                        break;
                    case 'error':
                        window.Toaster.error(message);
                        break;
                    case 'info':
                    default:
                        window.Toaster.info(message);
                        break;
                }
                console.log(`Toast dispatched: ${type} - ${message}`);
            }
        } else {
            // Log error and fallback to alert
            console.error('Toaster not available - message was:', message);
            alert(message);
        }
    };

    // Start the inactivity timers
    function startInactivityTimers() {
        // Clear any existing timeouts
        clearInactivityTimers();

        // Set warning timeout
        warningTimeout = setTimeout(function() {
            console.log('WARNING TIMEOUT FIRED - Showing 30-second warning toast');
            // Use persistent toast for warning
            showToast('warning', 'Your editing session will expire in 30 seconds due to inactivity', true);
            warningShown = true;
        }, WARNING_DELAY);

        // Set expiry timeout
        expiryTimeout = setTimeout(function() {
            console.log('EXPIRY TIMEOUT FIRED - Session expired, releasing lock and redirecting');
            showToast('warning', 'Your editing session has expired due to inactivity');

            // Release the lock with clear_all=true
            const releaseUrl = window.lockConfig ? window.lockConfig.getReleaseLockUrl(requestId) :
                `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`;

            console.log(`Releasing lock at URL: ${releaseUrl}`);
            fetch(releaseUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ clear_all: true })
            })
            .then(response => console.log('Lock release response:', response.status))
            .catch(error => console.error('Error releasing lock:', error));

            // Redirect to index page
            const indexRoute = window.lockConfig ? window.lockConfig.indexUrl :
                '/library/instruction-requests/public/dashboard/instructionRequests';
            setTimeout(() => window.location.href = indexRoute, 4000);
        }, EXPIRY_DELAY);

        console.log(`Inactivity timer started/reset - will warn after ${WARNING_DELAY/60000} minutes and expire after ${EXPIRY_DELAY/60000} minutes`);
    }

    // Clear inactivity timers
    function clearInactivityTimers() {
        if (warningTimeout) clearTimeout(warningTimeout);
        if (expiryTimeout) clearTimeout(expiryTimeout);

        // Dismiss any active warning toast
        if (warningToastInstance && typeof warningToastInstance.dismiss === 'function') {
            warningToastInstance.dismiss();
            warningToastInstance = null;
        }

        warningShown = false;
    }

    // Function to handle user activity
    function handleUserActivity() {
        console.log('User activity detected - resetting inactivity timer');
        // Reset inactivity timers
        startInactivityTimers();
    }

    // Setup event listeners for user activity
    // Listen for click events on the document (catches all UI interactions)
    document.addEventListener('click', handleUserActivity);

    // Listen for keyboard events on the document
    document.addEventListener('keydown', handleUserActivity);

    // Listen for touch events (mobile support)
    document.addEventListener('touchstart', handleUserActivity);

    // Listen for form-specific events on the edit form
    if (editForm) {
        // Track form interactions (focus, input, change)
        editForm.addEventListener('focus', handleUserActivity, true); // Use capturing
        editForm.addEventListener('input', handleUserActivity, true); // Use capturing
        editForm.addEventListener('change', handleUserActivity, true); // Use capturing

        console.log('Activity tracking set up on form and document (excluding mousemove)');
    }

    // Start initial inactivity timers
    startInactivityTimers();

    // Release lock when leaving page
    window.addEventListener('beforeunload', function() {
        // Get the release URL from the config
        const releaseUrl = window.lockConfig ? window.lockConfig.getReleaseLockUrl(requestId) :
            `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`;

        // Log that we're releasing the lock (for debugging)
        console.log(`Releasing lock for request ${requestId} due to page navigation/reload`);

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Use sendBeacon with proper JSON data
        // clear_all: false preserves lock ownership info (locked_by, locked_at) for reload detection
        navigator.sendBeacon(
            releaseUrl,
            new Blob([JSON.stringify({
                _token: token,
                clear_all: false  // Preserve lock info for reload detection
            })], { type: 'application/json' })
        );
    });
});
