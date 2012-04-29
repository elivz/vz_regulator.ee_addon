/*
 * Polyfill for the HTML5 form input `pattern` attribute
 */
(function($) {

    var vz_regulator = {

        /*
         * Start the script
         */
        init: function() {
            $('#publishForm').delegate('.vz_regulator_field', 'keyup', vz_regulator.check_validity);
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

            // Fake support, if necessary
            if (vz_regulator.not_natively_supported) {
                // Test against the regular expression
                var pattern = new RegExp($this.attr('pattern', 'g'));
                is_invalid = !pattern.test($this.val());

                // Set a class so we can style the input
                $this.toggleClass('invalid', is_invalid);
            } else {
                // Just use the invalidity status from the browser
                is_invalid = $this.is(':invalid');
            }

            // Update the error message
            vz_regulator.set_hint_visibility.apply(this, [is_invalid]);
        },

        set_hint_visibility : function(is_invalid) {
            var $this = $(this),
                $hint = $this.next();

            if (is_invalid) {
                $hint.fadeIn(200);
            } else {
                $hint.fadeOut(100);
            }
        }
    };

    // Document is ready
    //$(document).bind('ready', vz_regulator.init);

})(jQuery);
