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

// Choices.js for searchable selects
import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';
window.Choices = Choices;

document.addEventListener('DOMContentLoaded', () => {
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
