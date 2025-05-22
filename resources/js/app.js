console.log('App.js loaded');
import './bootstrap';
// Lock refresh modules are imported individually in their respective pages

// Import campus reordering module
import './campus-reorder';

// Import Toaster JavaScript
import '../../vendor/masmerise/livewire-toaster/resources/js';

// PowerGrid assets
import '../../vendor/power-components/livewire-powergrid/dist/tailwind.css'
import '../../vendor/power-components/livewire-powergrid/dist/powergrid'

// Flatpickr for date pickers
import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.min.css'

// Choices.js for select inputs
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

document.addEventListener('DOMContentLoaded', () => {
    // Initialize choices for librarian select - this is for campus editor
    const librarianSelect = document.querySelector('.choices-librarian-select');

    if (librarianSelect) {
        const choices = new Choices(librarianSelect, {
            removeItemButton: true,
            searchEnabled: true,
            placeholder: true,
            placeholderValue: 'click to add librarians',
            itemSelectText: 'Select',
            shouldSort: false, // This prevents alphabetical sorting
            classNames: {
                containerInner: 'choices__inner--custom', // Optional: for custom styling
            }
        });
    }

    // Handle form submission for librarian select - this is for campus editor
    const form = document.querySelector('form');
    const librarianIdsSelect = document.getElementById('librarian_ids');

    if (form && librarianIdsSelect) {
        form.addEventListener('submit', () => {
            // Ensure the select has the correct values before submission
            const choicesInstance = librarianIdsSelect.choices;
            if (choicesInstance) {
                const selectedValues = choicesInstance.getValue(true);
                librarianIdsSelect.value = selectedValues;
            }
        });
    }

    // Log when Livewire is initialized
    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized');
    });

    // Ensure page is fully initialized before Alpine.js takes over
    // This helps prevent flash issues with x-cloak and conditional content
    if (typeof Alpine !== 'undefined') {
        document.querySelectorAll('[x-cloak]').forEach(el => {
            // Ensure modals with x-cloak are properly hidden before Alpine initializes
            if (el.hasAttribute('x-show') && el.classList.contains('fixed') && el.classList.contains('inset-0')) {
                el.style.display = 'none';
            }
        });
    }
});
