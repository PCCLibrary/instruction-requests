/**
 * Table Lock Status Refresh
 *
 * Periodically refreshes the lock status in the InstructionRequestTable component
 * to ensure the table displays up-to-date lock information.
 */

// Configuration - easily adjust intervals here
const TABLE_REFRESH_INTERVAL = 30000; // 30 seconds - how often to refresh the table's lock status

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a page with the InstructionRequestTable
    const powerGridTable = document.querySelector('#power-grid-table-container') || document.querySelector('table.power-grid-table');

    if (powerGridTable) {
        console.log('PowerGrid table found, setting up 30-second refresh interval');
        // Refresh lock status every 30 seconds
        setInterval(function() {
            console.log('30-second interval triggered, attempting to refresh PowerGrid table');
            if (window.Livewire) {
                console.log('Livewire object found, dispatching refreshLockStatus event');
                window.Livewire.dispatch('refreshLockStatus');
            } else {
                console.log('Livewire object not found, unable to dispatch event');
            }
        }, TABLE_REFRESH_INTERVAL);
    } else {
        console.log('PowerGrid table not found, skipping table refresh setup');
        console.log('Attempted to find table with selectors: #power-grid-table-container, table.power-grid-table');
    }
});
