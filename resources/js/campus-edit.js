document.addEventListener('DOMContentLoaded', function() {
    const deleteBtn = document.getElementById('delete-campus-btn');
    const modal = document.getElementById('delete-modal');
    const cancelBtn = document.getElementById('cancel-delete');
    const modalTitle = document.getElementById('modal-title');
    const modalDescription = document.getElementById('modal-description');
    const impactDetails = document.getElementById('impact-details');
    const form = document.querySelector('form');

    if (!deleteBtn || !modal) return;

    // Google Calendar validation - prevent form submission if invalid
    if (form) {
        form.addEventListener('submit', function(e) {
            const gcalInput = document.getElementById('gcal');
            if (gcalInput && gcalInput.value.trim() !== '') {
                const pattern = /^c_[a-f0-9]+@group\.calendar\.google\.com$/;
                if (!pattern.test(gcalInput.value.trim())) {
                    e.preventDefault();
                    alert('Please enter a valid Google Calendar ID format before saving.');
                    gcalInput.focus();
                    return false;
                }
            }
        });
    }

    // Show modal and load impact data
    deleteBtn.addEventListener('click', async function() {
        modal.classList.remove('hidden');
        modalDescription.textContent = 'Loading impact analysis...';
        impactDetails.classList.add('hidden');

        try {
            const deleteImpactUrl = this.dataset.deleteImpactUrl;

            if (!deleteImpactUrl) {
                throw new Error('Delete impact URL not found');
            }

            const response = await fetch(deleteImpactUrl);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            const impact = await response.json();

            modalTitle.textContent = `Delete "${impact.campus_name}"?`;
            modalDescription.textContent = 'This action will remove the campus from all dropdowns and prevent new requests. Existing data will be preserved.';

            // Update impact details
            document.getElementById('requests-impact').innerHTML = `• <span class="font-medium">${impact.instruction_requests_count}</span> total instruction requests`;
            document.getElementById('active-impact').innerHTML = `• <span class="font-medium">${impact.active_requests_count}</span> active/in-progress requests`;
            document.getElementById('librarians-impact').innerHTML = `• <span class="font-medium">${impact.assigned_librarians_count}</span> assigned librarians`;

            impactDetails.classList.remove('hidden');
        } catch (error) {
            modalDescription.textContent = 'Error loading impact analysis. Please try again.';
            console.error('Error fetching delete impact:', error);
        }
    });

    // Hide modal
    cancelBtn.addEventListener('click', function() {
        modal.classList.add('hidden');
    });

    // Hide modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.add('hidden');
        }
    });

    // Handle escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
        }
    });
});
