/**
 * Edit Form Lock Refresh
 *
 * Handles form lock refresh and timeout notifications for the edit form.
 */

// Configuration - easily adjust intervals here
const LOCK_REFRESH_INTERVAL = 300000;    // 5 minutes - how often to refresh the lock while active
const INACTIVITY_CHECK_INTERVAL = 30000; // 30 seconds - how often to check for inactivity
const WARNING_THRESHOLD = 1.5;           // 1.5 minutes - when to show inactivity warning
const EXPIRY_THRESHOLD = 2;              // 2 minutes - when to expire session and release lock

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on an edit form page with lock functionality
    const editForm = document.querySelector('form.edit-form[data-request-id]');
    if (editForm) {
        // Check if Toaster is available to show notifications
        if (!window.Toaster) {
            console.error('Toaster is not initialized! Toast notifications may not work properly.');
        }

        const requestId = editForm.getAttribute('data-request-id');
        let lastActivity = Date.now();
        let inactivityTimeout = null;
        let warningShown = false;

        // Function to refresh the lock
        const refreshLock = function() {
            // Use the URL from the window.lockConfig object
            const refreshUrl = window.lockConfig ? window.lockConfig.getRefreshLockUrl(requestId) :
                `/library/instruction-requests/public/instructionRequests/${requestId}/refresh-lock`;

            fetch(refreshUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // If lock refresh failed, show toast and redirect
                    showToast('warning', data.message || 'Your editing session has expired');
                    // Get the correct index route from the config
                    const indexRoute = window.lockConfig ? window.lockConfig.indexUrl :
                        '/library/instruction-requests/public/dashboard/instructionRequests';
                    // Use slightly longer delay to ensure toast is visible
                    setTimeout(() => window.location.href = indexRoute, 4000);
                }
            })
            .catch(error => {
                console.error('Error refreshing lock:', error);
            });
        };

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

        // Function to check for inactivity
        const checkInactivity = function() {
            const inactiveTime = (Date.now() - lastActivity) / 1000 / 60; // minutes

            // Show warning at 1.5 minutes (reduced for testing)
            if (inactiveTime >= WARNING_THRESHOLD && !warningShown) {
                showToast('warning', 'Your editing session will expire in 30 seconds due to inactivity');
                warningShown = true;
            }

            // Redirect at 2 minutes (reduced for testing)
            if (inactiveTime >= EXPIRY_THRESHOLD) {
                showToast('warning', 'Your editing session has expired due to inactivity');

                // Get the release URL from the config
                const releaseUrl = window.lockConfig ? window.lockConfig.getReleaseLockUrl(requestId) :
                    `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`;

                // Release the lock
                fetch(releaseUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                }).catch(error => console.error('Error releasing lock:', error));

                // Get the correct index route from the config
                const indexRoute = window.lockConfig ? window.lockConfig.indexUrl :
                    '/library/instruction-requests/public/dashboard/instructionRequests';
                // Use slightly longer delay to ensure toast is visible
                setTimeout(() => window.location.href = indexRoute, 4000);
                clearInterval(inactivityTimeout);
            }
        };

        // Reset activity timestamp on user interaction
        const resetActivity = function() {
            lastActivity = Date.now();
            warningShown = false;
        };

        // Setup event listeners for user activity
        ['click', 'keydown', 'mousemove', 'touchstart'].forEach(eventType => {
            document.addEventListener(eventType, resetActivity);
        });

        // Log that inactivity timer has started
        console.log(`Edit page inactivity timer started - will expire after ${EXPIRY_THRESHOLD} minutes of inactivity`);

        // Refresh lock every 5 minutes if there has been activity
        setInterval(refreshLock, LOCK_REFRESH_INTERVAL);

        // Check for inactivity every 30 seconds for more responsive testing
        inactivityTimeout = setInterval(checkInactivity, INACTIVITY_CHECK_INTERVAL);

        // Release lock when leaving page
        window.addEventListener('beforeunload', function() {
            // Get the release URL from the config
            const releaseUrl = window.lockConfig ? window.lockConfig.getReleaseLockUrl(requestId) :
                `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`;

            navigator.sendBeacon(
                releaseUrl,
                new FormData(document.createElement('form'))
            );
        });
    }
});
