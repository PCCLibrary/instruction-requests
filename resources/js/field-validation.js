// Field validation function following campus editor pattern
// Only applies to create page - validates real-time with instruction-type context

function fieldValidation(fieldName, validationRules = null) {
    return {
        fieldName: fieldName,
        fieldValue: '',
        validationState: null, // null (no validation yet) or false (invalid) - no success states
        errorMessage: '',
        validationRules: validationRules,
        hasServerError: false,

        init() {
            // Initialize with current input value
            const input = this.$el.querySelector('input, select, textarea');
            this.fieldValue = input?.value || '';

            // Check for server-side errors - these take precedence
            this.hasServerError = this.$el.querySelector('.text-red-600:not([x-show])') !== null;

            // Register with global form validation state
            if (Alpine.store('instructionFormValidation')) {
                Alpine.store('instructionFormValidation').registerField(this.fieldName, this);
            }

            // Initial validation if field has a value
            if (this.fieldValue && this.fieldValue.trim() !== '') {
                this.validateField();
            }
        },

        validateField() {
            // Skip validation if field is hidden or has server errors
            if (!this.isFieldVisible() || this.hasServerError) {
                this.validationState = null;
                this.errorMessage = '';
                return;
            }

            // Get current instruction type for context-aware validation
            const instructionType = Alpine.store('instructionFormValidation')?.instructionType || '';

            // Apply validation rules
            const result = this.applyValidationRules(this.fieldValue, instructionType);

            // Only set error states (no success states)
            this.validationState = result.isValid === false ? false : null;
            this.errorMessage = result.errorMessage || '';

            // Update global form validation state
            if (Alpine.store('instructionFormValidation')) {
                Alpine.store('instructionFormValidation').updateFieldValidation(this.fieldName, result.isValid !== false);
            }
        },

        applyValidationRules(value, instructionType) {
            if (!this.validationRules) {
                return { isValid: true };
            }

            // Check if field is required based on context
            const isRequired = this.isFieldRequired(instructionType);

            // Required field validation
            if (isRequired && (!value || value.trim() === '')) {
                return {
                    isValid: false,
                    errorMessage: this.validationRules.messages?.required || `${this.fieldName} is required`
                };
            }

            // Skip further validation if empty and not required
            if (!value || value.trim() === '') {
                return { isValid: true };
            }

            // Pattern validation
            if (this.validationRules.pattern) {
                const pattern = this.validationRules.pattern;
                const regex = typeof pattern === 'string' ? new RegExp(pattern) : pattern;

                if (!regex.test(value)) {
                    return {
                        isValid: false,
                        errorMessage: this.validationRules.messages?.invalid || 'Invalid format'
                    };
                }
            }

            // Custom validation functions
            if (this.validationRules.customValidator) {
                const validatorName = this.validationRules.customValidator;
                if (window.customValidators && window.customValidators[validatorName]) {
                    return window.customValidators[validatorName](value, instructionType);
                }
            }

            // Min/max validation for numbers
            if (this.validationRules.min !== undefined) {
                const num = parseInt(value);
                if (!isNaN(num) && num < this.validationRules.min) {
                    return {
                        isValid: false,
                        errorMessage: this.validationRules.messages?.min || `Minimum value is ${this.validationRules.min}`
                    };
                }
            }

            // Valid - return success but no visual feedback
            return { isValid: true };
        },

        isFieldRequired(instructionType) {
            if (!this.validationRules) return false;

            // Always required fields
            if (this.validationRules.alwaysRequired) {
                return true;
            }

            // Context-specific required fields
            if (this.validationRules.requiredFor && Array.isArray(this.validationRules.requiredFor)) {
                return this.validationRules.requiredFor.includes(instructionType);
            }

            return false;
        },

        isFieldVisible() {
            // Check if the field or its parent containers are hidden
            let element = this.$el;
            while (element) {
                if (element.classList && element.classList.contains('hidden')) {
                    return false;
                }
                element = element.parentElement;
            }
            return true;
        },

        clearValidation() {
            this.validationState = null;
            this.errorMessage = '';
        },

        focusWithError() {
            // Focus the field and ensure error is visible
            const input = this.$el.querySelector('input, select, textarea');
            if (input) {
                input.focus();
                // Trigger validation to show error message
                this.validateField();
            }
        }
    };
}

// Make function globally available
window.fieldValidation = fieldValidation;
