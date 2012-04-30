/*
 * Polyfill for the HTML5 form input `pattern` attribute
 */
(function($) {

    var vz_regulator = {

        /*
         * Start the script
         */
        init: function() {
            // Initial check
            $('.vz_regulator_field').each(vz_regulator.check_validity);

            // Continute checking after each keystroke
            $('#publishForm').delegate('.vz_regulator_field', 'keyup', vz_regulator.check_validity);
        },

        /*
         * Check the validity of the input after every keystroke
         */
        check_validity : function() {
            var is_invalid = false;

            if (this.value !== '') {
                // Test against the regular expression
                var pattern = new RegExp(this.getAttribute('pattern'), 'g');
                is_invalid = !pattern.test(this.value);
            }

            // Set a class so we can style the input
            $(this).toggleClass('invalid', is_invalid);
        },

        /*
         * Test for native browser support of the pattern attribute
         */
        not_natively_supported : (function() {
            var input_element = document.createElement('input');
            return !('pattern' in input_element);
        })()
    };

    // Document is ready
    if (vz_regulator.not_natively_supported) {
        $(document).bind('ready', vz_regulator.init);
    }

})(jQuery);
