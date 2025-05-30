// Custom validation functions for complex rules
// Provides instruction-request-specific validation logic

window.customValidators = {
    validatePositiveInteger(value, instructionType) {
        // Remove any whitespace
        const trimmedValue = value.trim();

        // Check if it's a valid integer
        const pattern = /^\d+$/;
        if (!pattern.test(trimmedValue)) {
            return {
                isValid: false,
                errorMessage: 'Please enter a whole number (integers only)'
            };
        }

        const num = parseInt(trimmedValue);
        if (num < 1) {
            return {
                isValid: false,
                errorMessage: 'Please enter 1 or more students'
            };
        }

        return { isValid: true };
    },

    validateCourseNumber(value, instructionType) {
        const trimmedValue = value.trim();

        // Pattern matches: digits only, digits with letter suffix, mixed patterns
        // Examples: 101, 102A, 20B, 1A2, etc.
        const pattern = /^(\d{1,3}[A-Za-z]?|\d{2,3}[A-Za-z]|\d{1,2}[A-Za-z]\d?|\d{0,2}[A-Za-z]\d|\d+)$/;

        if (!pattern.test(trimmedValue)) {
            return {
                isValid: false,
                errorMessage: 'Please enter a valid course number (e.g., 101, 102A, 20B)'
            };
        }

        return { isValid: true };
    },

    validateCRN(value, instructionType) {
        const trimmedValue = value.trim();

        // Must be exactly 5 digits
        const pattern = /^\d{5}$/;

        if (!pattern.test(trimmedValue)) {
            return {
                isValid: false,
                errorMessage: 'Please enter a 5-digit CRN number (numbers only)'
            };
        }

        return { isValid: true };
    },

    validateEmail(value, instructionType) {
        const trimmedValue = value.trim();

        // Basic email pattern
        const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!pattern.test(trimmedValue)) {
            return {
                isValid: false,
                errorMessage: 'Please enter a valid email address'
            };
        }

        return { isValid: true };
    },

    validateDateTime(value, instructionType) {
        if (!value) {
            // Check if required for this instruction type
            if (['on-campus', 'remote'].includes(instructionType)) {
                return {
                    isValid: false,
                    errorMessage: 'Preferred date and time is required'
                };
            }
            return { isValid: true };
        }

        const selectedDateTime = new Date(value);
        const minDateTime = new Date(Date.now() + 30 * 60 * 1000); // 30 minutes from now

        if (selectedDateTime <= minDateTime) {
            return {
                isValid: false,
                errorMessage: 'Please select a time in the future'
            };
        }

        return { isValid: true };
    },

    validateAlternateDateTime(value, instructionType) {
        // Alternate datetime is optional, but if provided must be in future
        if (!value || value.trim() === '') {
            return { isValid: true };
        }

        const selectedDateTime = new Date(value);
        const minDateTime = new Date(Date.now() + 30 * 60 * 1000); // 30 minutes from now

        if (selectedDateTime <= minDateTime) {
            return {
                isValid: false,
                errorMessage: 'Please select a time in the future'
            };
        }

        return { isValid: true };
    },

    validateAsyncDate(value, instructionType) {
        if (!value) {
            if (instructionType === 'asynchronous') {
                return {
                    isValid: false,
                    errorMessage: 'Ready date is required'
                };
            }
            return { isValid: true };
        }

        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time to start of day

        if (selectedDate < today) {
            return {
                isValid: false,
                errorMessage: 'Please select today or a future date'
            };
        }

        return { isValid: true };
    },

    validateDuration(value, instructionType) {
        if (!value) {
            // Check if required for this instruction type
            if (['on-campus', 'remote'].includes(instructionType)) {
                return {
                    isValid: false,
                    errorMessage: 'Duration is required'
                };
            }
            return { isValid: true };
        }

        const trimmedValue = value.trim();

        // Check if it's a valid integer
        const pattern = /^\d+$/;
        if (!pattern.test(trimmedValue)) {
            return {
                isValid: false,
                errorMessage: 'Please enter a whole number (integers only)'
            };
        }

        const num = parseInt(trimmedValue);
        if (num < 2) {
            return {
                isValid: false,
                errorMessage: 'Please enter a number greater than 1'
            };
        }

        return { isValid: true };
    }
};
