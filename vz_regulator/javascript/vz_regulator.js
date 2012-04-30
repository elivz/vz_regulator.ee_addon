/*
 * Polyfill for the HTML5 form input `pattern` attribute
 */
(function($) {

    var vz_regulator = {

        /*
         * Start the script
         */
        init: function() {
            if (vz_regulator.not_natively_supported) {
                // Initial check
                $('.vz_regulator_field').each(vz_regulator.check_validity);

                // Continute checking after each keystroke
                $('#publishForm').delegate('.vz_regulator_field', 'keyup', vz_regulator.check_validity);
            }
        },

        /*
         * Test for native browser support of the pattern attribute
         */
        not_natively_supported : (function() {
            var input_element = document.createElement('input');
            return !('pattern' in input_element);
        })(),

        /*
         * Check the validity of the input after every keystroke
         */
        check_validity : function() {
            var $this = $(this),
                is_invalid = false;

            if ($this.val() !== '') {
                // Test against the regular expression
                var pattern = new RegExp($this.attr('patternx'), 'g');
                is_invalid = !pattern.test($this.val());
            }

            // Set a class so we can style the input
            $this.toggleClass('invalid', is_invalid);
        }
    };

    // Document is ready
    $(document).bind('ready', vz_regulator.init);

})(jQuery);
