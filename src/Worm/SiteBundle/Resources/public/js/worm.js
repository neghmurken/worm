(function ($) {
    $(document).ready(function () {

        $('#login-form').each(function() {
            var $el = $(this);
            if($el.find('form .alert-error').length) {
                $el.find('a.dropdown-toggle').trigger('click');
            }
        });

    });
})(jQuery);