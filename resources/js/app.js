console.log('App.js loaded');
import './bootstrap';

// PowerGrid assets
import '../../vendor/power-components/livewire-powergrid/dist/tailwind.css'
import '../../vendor/power-components/livewire-powergrid/dist/powergrid'

// resources/js/app.js

import flatpickr from "flatpickr";
import 'flatpickr/dist/flatpickr.min.css'


import Choices from 'choices.js';
import 'choices.js/public/assets/styles/choices.min.css';

document.addEventListener('DOMContentLoaded', () => {
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
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form');
    const librarianSelect = document.getElementById('librarian_ids');

    if (form && librarianSelect) {
        form.addEventListener('submit', () => {
            // Ensure the select has the correct values before submission
            const choicesInstance = librarianSelect.choices;
            if (choicesInstance) {
                const selectedValues = choicesInstance.getValue(true);
                librarianSelect.value = selectedValues;
            }
        });
    }
});
