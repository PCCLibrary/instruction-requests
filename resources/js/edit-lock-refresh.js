/**
 * Edit Form Lock Refresh
 *
 * Handles form lock refresh and timeout notifications for the edit form.
 */

// Configuration - easily adjust intervals here
const WARNING_DELAY = 90000;    // 1.5 minutes (90000ms) - when to show inactivity warning
const EXPIRY_DELAY = 120000;    // 2 minutes (120000ms) - when to expire session and release lock

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

    // Function to show a toast message
    const showToast = function(type, message) {
        // Check if Toaster library is available
        if (window.Toaster) {
            // Use the correct method based on the type
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
            showToast('warning', 'Your editing session will expire in 30 seconds due to inactivity');
            warningShown = true;
        }, WARNING_DELAY);

        // Set expiry timeout
        expiryTimeout = setTimeout(function() {
            showToast('warning', 'Your editing session has expired due to inactivity');

            // Release the lock with clear_all=true
            const releaseUrl = window.lockConfig ? window.lockConfig.getReleaseLockUrl(requestId) :
                `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`;

            fetch(releaseUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ clear_all: true })
            }).catch(error => console.error('Error releasing lock:', error));

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
        warningShown = false;
    }

    // Function to handle user activity
    function handleUserActivity() {
        // Reset inactivity timers
        startInactivityTimers();
    }

    // Setup event listeners for user activity
    ['click', 'keydown', 'mousemove', 'touchstart', 'focus'].forEach(eventType => {
        document.addEventListener(eventType, handleUserActivity);
    });

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
