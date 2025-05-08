/**
 * Lock Status Refresh
 *
 * Periodically refreshes the lock status in the InstructionRequestTable component
 * to ensure the table displays up-to-date lock information.
 *
 * Also handles form lock refresh and timeout notifications.
 */
document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a page with the InstructionRequestTable
    if (document.querySelector('.powergrid-table')) {
        // Refresh lock status every 30 seconds
        setInterval(function() {
            if (window.Livewire) {
                window.Livewire.dispatch('refreshLockStatus');
            }
        }, 30000); // 30 seconds
    }

    // Check if we're on an edit form page with lock functionality
    const editForm = document.querySelector('form.edit-form[data-request-id]');
    if (editForm) {
        const requestId = editForm.getAttribute('data-request-id');
        let lastActivity = Date.now();
        let inactivityTimeout = null;
        let warningShown = false;

        // Function to refresh the lock
        const refreshLock = function() {
            fetch(`/library/instruction-requests/public/instructionRequests/${requestId}/refresh-lock`, {
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
                    // Get the correct index route
                    const indexRoute = '/library/instruction-requests/public/dashboard/instructionRequests';
                    setTimeout(() => window.location.href = indexRoute, 3000);
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
                window.Toaster.toast(message, type);
            } else if (window.Livewire) {
                // Fallback to Livewire flash message
                window.Livewire.dispatch('flash-message', {
                    type: type,
                    message: message
                });
            } else {
                // Fallback to alert if nothing else is available
                alert(message);
            }
        };

        // Function to check for inactivity
        const checkInactivity = function() {
            const inactiveTime = (Date.now() - lastActivity) / 1000 / 60; // minutes

            // Show warning at 14 minutes
            if (inactiveTime >= 14 && !warningShown) {
                showToast('warning', 'Your editing session will expire in 1 minute due to inactivity');
                warningShown = true;
            }

            // Redirect at 15 minutes
            if (inactiveTime >= 2) {
                showToast('warning', 'Your editing session has expired due to inactivity');
                // Release the lock
                fetch(`/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                    },
                }).catch(error => console.error('Error releasing lock:', error));

                // Redirect after a short delay to allow the toast to be seen
                const indexRoute = '/library/instruction-requests/public/dashboard/instructionRequests';
                setTimeout(() => window.location.href = indexRoute, 3000);
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

        // Refresh lock every 5 minutes if there has been activity
        setInterval(refreshLock, 300000); // 5 minutes

        // Check for inactivity every minute
        inactivityTimeout = setInterval(checkInactivity, 60000); // 1 minute

        // Release lock when leaving page
        window.addEventListener('beforeunload', function() {
            navigator.sendBeacon(
                `/library/instruction-requests/public/instructionRequests/${requestId}/release-lock`,
                new FormData(document.createElement('form'))
            );
        });
    }
});
