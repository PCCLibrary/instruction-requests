// Instruction form validation coordination
// Manages global validation state and form submission

// Only run on create page
if (window.location.pathname.includes('/create')) {

    // Enhanced instruction type settings with validation context
    window.instructionTypeSettings = {
        'on-campus': {
            required: ['number_of_students', 'campus_id', 'preferred_datetime', 'alternate_datetime', 'duration', 'department', 'course_number', 'course_crn'],
            visible: [
                'campus_id', 'librarian_id', 'department', 'course_number', 'course_crn',
                'number_of_students', 'ada_provisions_needed', 'ada_provisions_description',
                'preferred_datetime', 'alternate_datetime', 'duration', 'extra_time_with_class',
                'received_assignment', 'selected_topics', 'explored_background', 'written_draft',
                'other_learning_outcome', 'other_learning_outcome_description',
                'library_instruction_description', 'genai_discussion_interest',
                'class_description', 'materials', 'assignment_description'
            ],
            hidden: ['asynchronous_instruction_ready_date']
        },
        'remote': {
            required: ['number_of_students', 'campus_id', 'preferred_datetime', 'alternate_datetime', 'duration', 'department', 'course_number', 'course_crn'],
            visible: [
                'campus_id', 'librarian_id', 'department', 'course_number', 'course_crn',
                'number_of_students', 'ada_provisions_needed', 'ada_provisions_description',
                'preferred_datetime', 'alternate_datetime', 'duration',
                'received_assignment', 'selected_topics', 'explored_background', 'written_draft',
                'other_learning_outcome', 'other_learning_outcome_description',
                'library_instruction_description', 'genai_discussion_interest',
                'class_description', 'materials', 'assignment_description'
            ],
            hidden: ['asynchronous_instruction_ready_date', 'extra_time_with_class']
        },
        'asynchronous': {
            required: ['number_of_students', 'asynchronous_instruction_ready_date', 'campus_id', 'department', 'course_number', 'course_crn'],
            visible: [
                'campus_id', 'librarian_id', 'department', 'course_number', 'course_crn',
                'number_of_students', 'asynchronous_instruction_ready_date',
                'library_instruction_description', 'genai_discussion_interest',
                'class_description', 'materials', 'assignment_description'
            ],
            hidden: [
                'ada_provisions_needed', 'ada_provisions_description',
                'preferred_datetime', 'alternate_datetime', 'duration', 'extra_time_with_class',
                'received_assignment', 'selected_topics', 'explored_background', 'written_draft',
                'other_learning_outcome', 'other_learning_outcome_description'
            ]
        }
    };

    // Initialize Alpine.js stores when Alpine is ready
    document.addEventListener('alpine:init', () => {
        Alpine.store('instructionFormValidation', {
            instructionType: '',
            fields: {},
            isFormValid: false,

            registerField(fieldName, fieldComponent) {
                this.fields[fieldName] = fieldComponent;
                this.updateFormValidity();
            },

            updateFieldValidation(fieldName, isValid) {
                // Update overall form validity after any field changes
                this.updateFormValidity();
            },

            updateFormValidity() {
                const requiredFields = this.getRequiredFields();
                this.isFormValid = requiredFields.every(fieldName => {
                    const field = this.fields[fieldName];
                    if (!field) return true; // Field not registered yet

                    // Only check fields that are visible
                    if (!field.isFieldVisible()) return true;

                    return field.validationState !== false;
                });
            },

            getRequiredFields() {
                // Always required fields regardless of instruction type
                const alwaysRequired = ['name', 'email', 'instruction_type'];

                // Get instruction-type specific required fields
                const settings = this.instructionType && window.instructionTypeSettings
                    ? window.instructionTypeSettings[this.instructionType]
                    : null;

                const typeRequired = settings?.required || [];

                return [...alwaysRequired, ...typeRequired];
            },

            clearFieldValidation(fieldName) {
                if (this.fields[fieldName]) {
                    this.fields[fieldName].clearValidation();
                }
                this.updateFormValidity();
            },

            validateAllFields() {
                let allValid = true;
                const requiredFields = this.getRequiredFields();

                // Validate all registered fields that are visible
                Object.keys(this.fields).forEach(fieldName => {
                    const field = this.fields[fieldName];
                    if (field && field.isFieldVisible()) {
                        field.validateField();
                        if (field.validationState === false) {
                            allValid = false;
                        }
                    }
                });

                this.updateFormValidity();
                return allValid;
            },

            focusFirstError() {
                // Find first visible field with an error
                const fieldNames = this.getRequiredFields();

                for (const fieldName of fieldNames) {
                    const field = this.fields[fieldName];
                    if (field && field.isFieldVisible() && field.validationState === false) {
                        field.focusWithError();
                        return;
                    }
                }

                // If no required field errors, check all fields
                for (const fieldName of Object.keys(this.fields)) {
                    const field = this.fields[fieldName];
                    if (field && field.isFieldVisible() && field.validationState === false) {
                        field.focusWithError();
                        return;
                    }
                }
            },

            clearHiddenFieldValidation(instructionType) {
                const settings = window.instructionTypeSettings[instructionType];
                if (settings?.hidden) {
                    settings.hidden.forEach(fieldName => {
                        this.clearFieldValidation(fieldName);
                    });
                }
            },

            revalidateVisibleFields() {
                // Trigger validation on all visible fields after instruction type change
                Object.keys(this.fields).forEach(fieldName => {
                    const field = this.fields[fieldName];
                    if (field && field.isFieldVisible() && field.fieldValue && field.fieldValue.trim() !== '') {
                        field.validateField();
                    }
                });
            }
        });
    });

    // Form submission enhancement function
    window.enhanceFormSubmission = function(event, formValidationStore) {
        // Only prevent submission if there are validation errors
        if (formValidationStore && !formValidationStore.validateAllFields()) {
            event.preventDefault();
            formValidationStore.focusFirstError();
            return false;
        }

        // Allow normal form submission to proceed
        return true;
    };

} // End create page check
