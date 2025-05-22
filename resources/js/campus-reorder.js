import Sortable from 'sortablejs';

export function initializeCampusReordering() {
    const sortableTable = document.getElementById('sortable-table');

    if (!sortableTable) {
        return;
    }

    let sortable = new Sortable(sortableTable, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        dragClass: 'sortable-drag',
        onEnd: function(evt) {
            // Get the new order of IDs
            const orderedIds = Array.from(sortableTable.children).map(row => {
                return parseInt(row.getAttribute('data-id'));
            });

            // Call Livewire method to update order
            Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'))
                .call('updateOrder', orderedIds);
        }
    });

    // Add CSS classes for drag states
    const style = document.createElement('style');
    style.textContent = `
        .sortable-ghost {
            opacity: 0.4;
        }
        .sortable-chosen {
            background-color: #f3f4f6;
        }
        .sortable-drag {
            opacity: 0.8;
            transform: rotate(2deg);
        }
    `;
    document.head.appendChild(style);
}

// Auto-initialize when the DOM is ready
document.addEventListener('DOMContentLoaded', initializeCampusReordering);

// Re-initialize when Livewire updates the component
document.addEventListener('livewire:navigated', initializeCampusReordering);
