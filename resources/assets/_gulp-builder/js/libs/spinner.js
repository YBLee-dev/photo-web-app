/*
 * Spinner functions.
 */
$(document).ready(function() {
    /**
     * Create jQuery function.
     */
    (function ($) {
        /*
         * Adding spinner into element.
         * @param spinnerId{String} - id of spinner
         */
        $.fn.spinnerAdd = function (spinnerId) {
            if(!spinnerId) spinnerId = 'js_spinner';
            $(this).append('<div id="'+spinnerId+'" class="loaderWrapper"><div class="loader"></div></div>');
        };
        /*
         * Remove spinner.
         * @param{String} spinnerId - id of spinner
         */
        $.spinnerRemove= function (spinnerId) {
            if(!spinnerId) spinnerId = 'js_spinner';
            $('#'+spinnerId + '.loaderWrapper').remove();
        };
        /*
         * Adding spinner into body.
         * @param{String} spinnerId - id of spinner
         */
        $.spinnerAdd = function(spinnerId){
            if(!spinnerId) spinnerId = 'js_spinner';
            $('body').append('<div id="'+spinnerId+'" class="loaderWrapper"><div class="loader"></div></div>');
        }
    })(jQuery);
});
