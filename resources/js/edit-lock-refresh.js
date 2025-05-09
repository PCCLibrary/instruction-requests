/**
 * Edit Form Lock Refresh
 *
 * Handles form lock refresh using server-provided lock state information.
 * Uses server as the source of truth for lock state and improves reliability.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on an edit form page with lock functionality
    const editForm = document.querySelector('form.edit-form[data-request-id]');
    if (!editForm) return;

    // Check if lockConfig and lockState are available
    if (!window.lockConfig) {
        console.error('lockConfig is not initialized! Lock refresh will not work properly.');
        return;
    }

    // Ensure Toaster is available for notifications
    if (!window.Toaster) {
        console.error('Toaster is not initialized! Toast notifications may not work properly.');
    }

    // Configuration - easily adjust intervals here
    const LOCK_REFRESH_INTERVAL = 300000;    // 5 minutes - how often to refresh the lock while active
    const INACTIVITY_CHECK_INTERVAL = 30000; // 30 seconds - how often to check for inactivity
    const WARNING_THRESHOLD = 1.5;           // 1.5 minutes - when to show inactivity warning
    const EXPIRY_THRESHOLD = 2;              // 2 minutes - when to expire session due to inactivity

    // Get the request ID from the form
    const requestId = editForm.getAttribute('data-request-id');

    // Initialize activity tracking
    let lastActivity = Date.now();
    let inactivityTimeout = null;
    let warningShown = false;
    let lockRefreshInterval = null;

    // Set up inactivity monitoring and lock refreshing only if we have the lock
    if (window.lockConfig.lockState && window.lockConfig.lockState.hasLock) {
        setupLockRefreshing();
        setupInactivityMonitoring();
        setupNavigationHandler();

        console.log(`Edit page lock monitoring started - you have the lock for request ${requestId}`);
    } else {
        // If we don't have the lock, don't set up any lock refreshing or monitoring
        console.log('Edit page loaded in view-only mode - you do not have the lock');
    }

    /**
     * Set up periodic lock refreshing
     */
    function setupLockRefreshing() {
        // Refresh lock every LOCK_REFRESH_INTERVAL
        lockRefreshInterval = setInterval(refreshLock, LOCK_REFRESH_INTERVAL);

        // Initial lock refresh to ensure we have it
        refreshLock();
    }

    /**
     * Set up inactivity monitoring
     */
    function setupInactivityMonitoring() {
        // Reset activity timestamp on user interaction
        ['click', 'keydown', 'mousemove', 'touchstart'].forEach(eventType => {
            document.addEventListener(eventType, resetActivity);
        });

        // Check for inactivity every INACTIVITY_CHECK_INTERVAL
        inactivityTimeout = setInterval(checkInactivity, INACTIVITY_CHECK_INTERVAL);

        console.log(`Inactivity timer started - session will expire after ${EXPIRY_THRESHOLD} minutes of inactivity`);
    }

    /**
     * Handle page navigation to release lock
     */
    function setupNavigationHandler() {
        // Release lock when leaving page
        window.addEventListener('beforeunload', function() {
            // Get the release URL from the config
            const releaseUrl = window.lockConfig.getReleaseLockUrl(requestId);

            // Use sendBeacon for reliable delivery during page navigation
            navigator.sendBeacon(
                releaseUrl,
                new Blob([JSON.stringify({
                    '_token': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                })], { type: 'application/json' })
            );

            console.log(`Released lock for request ${requestId} due to page navigation`);
        });
    }

    /**
     * Function to refresh the lock
     */
    function refreshLock() {
        // Skip if we don't have the lock according to server state
        if (!window.lockConfig.lockState || !window.lockConfig.lockState.hasLock) {
            console.log('Not refreshing lock - we do not have the lock');
            return;
        }

        // Use the URL from the window.lockConfig object
        const refreshUrl = window.lockConfig.getRefreshLockUrl(requestId);

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
                handleLockLost(data.message || 'Your editing session has expired');
            }
        })
        .catch(error => {
            console.error('Error refreshing lock:', error);
            showToast('error', 'Network error occurred while refreshing your editing session');
        });
    }

    /**
     * Function to show a toast message
     */
    function showToast(type, message) {
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
    }

    /**
     * Function to check for inactivity
     */
    function checkInactivity() {
        // Skip if we don't have the lock according to server state
        if (!window.lockConfig.lockState || !window.lockConfig.lockState.hasLock) {
            return;
        }

        const inactiveTime = (Date.now() - lastActivity) / 1000 / 60; // minutes

        // Show warning at WARNING_THRESHOLD minutes
        if (inactiveTime >= WARNING_THRESHOLD && !warningShown) {
            const secondsRemaining = Math.round((EXPIRY_THRESHOLD - WARNING_THRESHOLD) * 60);
            showToast('warning', `Your editing session will expire in ${secondsRemaining} seconds due to inactivity`);
            warningShown = true;
        }

        // Handle expiry at EXPIRY_THRESHOLD minutes
        if (inactiveTime >= EXPIRY_THRESHOLD) {
            handleLockLost('Your editing session has expired due to inactivity');
        }
    }

    /**
     * Handle lost lock (inactivity timeout, server error, etc.)
     */
    function handleLockLost(message) {
        showToast('warning', message);

        // Release the lock explicitly
        const releaseUrl = window.lockConfig.getReleaseLockUrl(requestId);
        fetch(releaseUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            // Add clear_all=true to fully clear lock info
            body: JSON.stringify({ clear_all: true })
        }).catch(error => console.error('Error releasing lock:', error));

        // Clean up intervals
        if (inactivityTimeout) clearInterval(inactivityTimeout);
        if (lockRefreshInterval) clearInterval(lockRefreshInterval);

        // Redirect to index page after a delay
        const indexRoute = window.lockConfig.indexUrl || '/dashboard/instructionRequests';
        setTimeout(() => window.location.href = indexRoute, 4000);
    }

    /**
     * Reset activity timestamp on user interaction
     */
    function resetActivity() {
        lastActivity = Date.now();
        warningShown = false;
    }
});
