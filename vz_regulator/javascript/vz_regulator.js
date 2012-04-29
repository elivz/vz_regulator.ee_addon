/*
 * Polyfill for the HTML5 form input `pattern` attribute
 */
(function($) {

    var vz_regulator = {

        /*
         * Start the script
         */
        init: function() {
            $('body').delegate('.vz_regulator_input', {
                'keyup': vz_regulator.event_handlers.keyup
            });
        },

        /*
         * Test for native browser support of the pattern attribute
         */
        is_natively_supported : function() {
            var input_element = document.createElement('input');
            return !!('pattern' in input_element);
        },

        event_handlers : {

            /*
             * Check the validity of the input after every keystroke
             */
            keyup : function() {

            }

        },

        check_pattern : function() {
            if (!this.element.attr('pattern')) {
                return true;
            }
            if (this.element.attr('title')) {
                this.validationMessage.patternMismatch = this.element.attr('title');
            }
            var pattern = new RegExp(this.element.attr('pattern'), 'g');
            this.validity.patternMismatch = pattern.test(this.element.val());
        },

        updateStatus: function() {
            this.element.removeClass('valid').removeClass('invalid');
            this.element.addClass(this.isValid() ? 'valid' : 'invalid');
        },
        showTooltip : function(error) {
           if (!error) {
                error = this.getValidationMessage(this);
           }
           $.setCustomValidityCallback.apply(this.element, [error]);
        }
    };

    /**
     * Renders tooltip when validation error happens on form submition
     * Can be overriden
     */
     $.setCustomValidityCallback = function(error) {
       var pos = this.position(),
       tooltip = $('<div class="tooltip tooltip-e">'
           + '<div class="tooltip-arrow tooltip-arrow-e"></div>'
           + '<div class="tooltip-inner">' + error + '</div>'
       + '</div>').appendTo(this.parent());
       tooltip.css('top', pos.top - (tooltip.height() / 2) + 20 );
       tooltip.css('left', pos.left - tooltip.width() - 12);
       window.setTimeout(function(){
            tooltip.remove();
       }, 2500);
    }
    /**
     * Shim for setCustomValidity DOM element method
     */
    $.fn.setCustomValidity = function(error) {
        this.each(function() {
            if (typeof $(this).get(0).setCustomValidity === 'function') {
                $(this).get(0).setCustomValidity(error);
            }
            $(this).data('customvalidity', error);
        });
    }

    // Document is ready
    $(document).bind('ready', vz_regulator.init);


})( jQuery );
