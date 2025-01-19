// $(document).ready(function() {
//     /**
//      * Hide all fieldsets and destroy Select2 instances if initialized.
//      */
//     function hideAllFieldsets() {
//         $('.on-campus, .remote, .asynchronous').addClass('d-none').each(function() {
//             if ($(this).find('select').data('select2')) {
//                 $(this).find('select').select2('destroy');
//             }
//         });
//     }
//
//     /**
//      * Apply field settings based on the selected instruction type.
//      * @param {string} value - The selected instruction type value.
//      */
//     function applyFieldSettings(value) {
//         hideAllFieldsets(); // Hide all fieldsets first.
//
//         if (value && value in fieldSettings) {
//             const settings = fieldSettings[value];
//
//             // Show the relevant fieldset
//             $(settings.show).removeClass('d-none').find('select').select2({
//                 theme: 'bootstrap4',
//                 width: '100%'
//             });
//
//             // Enable all fields before applying specific disabled settings
//             $('.on-campus, .remote, .asynchronous').find('input, select, textarea').prop('disabled', false);
//
//             // Set fields as required
//             $.each(settings.required, function(index, selector) {
//                 $(selector).prop('required', true);
//                 $(`label[for="${selector.substring(1)}"]`).addClass('is-required');
//             });
//
//             // Set fields as not required
//             $.each(settings.notRequired, function(index, selector) {
//                 $(selector).prop('required', false);
//                 $(`label[for="${selector.substring(1)}"]`).removeClass('is-required');
//             });
//
//             // Disable specific fields
//             $.each(settings.disable, function(index, selector) {
//                 $(selector).prop('disabled', true);
//             });
//         }
//     }
//
//     const fieldSettings = {
//         'on-campus': {
//             show: '.on-campus',
//             required: ['#number_of_students', '#campus_id', '#preferred_datetime', '#duration'],
//             notRequired: ["#librarian_id", "#asynchronous_instruction_ready_date", "#alternate_datetime"],
//             disable: ["#asynchronous_instruction_ready_date"]
//         },
//         'remote': {
//             show: '.remote',
//             required: ['#librarian_id', '#preferred_datetime', '#duration', '#campus_id'],
//             notRequired: ['#number_of_students', "#asynchronous_instruction_ready_date", "#alternate_datetime"],
//             disable: ["#asynchronous_instruction_ready_date"]
//         },
//         'asynchronous': {
//             show: '.asynchronous',
//             required: ['#asynchronous_instruction_ready_date', '#campus_id'],
//             notRequired: ['#librarian_id', '#number_of_students', '#preferred_datetime', '#alternate_datetime', '#duration'],
//             disable: ['#librarian_id', '#preferred_datetime', '#alternate_datetime', '#duration']
//         }
//     };
//
//     // Initialize select elements
//     $('#librarian_id, #department').select2({
//         theme: 'bootstrap4',
//         width: '100%',
//         placeholder: function() {
//             return $(this).data('placeholder');
//         }
//     });
//
//     let instructionTypeSelect = $('select[name="instruction_type"]');
//     applyFieldSettings(instructionTypeSelect.val()); // Apply initial settings
//
//     instructionTypeSelect.change(function() {
//         applyFieldSettings($(this).val());
//     });
// });
