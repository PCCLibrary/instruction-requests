$(document).ready(function(){
    $('#instructionRequestForm').validate({
        messages: {
            // name: "Please enter your name.",
            // email: 'Please enter your email.',
            // instruction_type: 'Please select the type of instruction you need.'
        }
    });
});

$(document).ready(function() {
    // Attach event listener to checkboxes with the class 'toggle-checkbox'
    $('.toggle-checkbox').change(function() {
        // Get the data-target value from the checkbox
        let targetDivId = $(this).data('target');

        // Toggle the visibility of the corresponding div
        $('#' + targetDivId).toggleClass('invisible');
    });
});

$('#clearForm').click(function() {
    // Target your form by its unique ID or class
    $('#instructionRequestForm').find('input:text, input:password, input:file, select, textarea').val('');
    $('#instructionRequestForm').find('input:radio, input:checkbox').prop('checked', false);
    $('#instructionRequestForm').find('select').prop('selectedIndex', 0); // Resets all select boxes to their first option
    $('#ada_provisions_description').addClass('invisible');
});

$(document).ready(function() {
    function hideAllFieldsets() {
        $('.on-campus, .remote, .asynchronous').addClass('d-none');
    }

    function applyFieldSettings(value) {
        if (value && value in fieldSettings) {
            const settings = fieldSettings[value];
            $(settings.show).removeClass('d-none');

            $.each(settings.required, function(index, selector) {
                $(selector).prop('required', true);
                $(`label[for="${selector.substring(1)}"]`).addClass('is-required');
            });

            $.each(settings.notRequired, function(index, selector) {
                $(selector).prop('required', false);
                $(`label[for="${selector.substring(1)}"]`).removeClass('is-required');
            });
        } else {
            hideAllFieldsets(); // Hide all fieldsets if no valid instruction type is selected
        }
    }

    const fieldSettings = {
        'on-campus': {
            show: '.on-campus',
            required: ['#number_of_students', '#campus_id', '#preferred_datetime','#duration'],
            notRequired: ['#librarian_id', "#asynchronous_instruction_ready_date"],
        },
        'remote': {
            show: '.remote',
            required: ['#librarian_id', '#preferred_datetime', '#duration', '#campus_id'],
            notRequired: ['#number_of_students', "#asynchronous_instruction_ready_date"],
        },
        'asynchronous': {
            show: '.asynchronous',
            required: ['#asynchronous_instruction_ready_date', '#campus_id'],
            notRequired: ['#librarian_id', '#number_of_students', '#preferred_datetime', '#alternate_datetime', '#duration'],
        }
    };

    // Apply settings based on initial select value on page load
    const initialType = $('select[name="instruction_type"]').val();
    applyFieldSettings(initialType);

    // Apply settings when select value changes
    $('select[name="instruction_type"]').change(function() {
        applyFieldSettings($(this).val());
    });


    $('#librarian_id').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select Librarian'
    });

    $('#department').select2({
        theme: 'bootstrap4',
        width: '100%',
        placeholder: 'Select your subject'
    });

});
