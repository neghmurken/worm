(function ($) {
    $(document).ready(function () {

        $('#login-form').each(function() {
            var $el = $(this);
            if($el.find('form .alert-error').length) {
                $el.find('a.dropdown-toggle').trigger('click');
            }
        });

        $('.submit-form').each(function () {
            var $form = $(this),
                $fileInput = $form.find('[type=file]'),
                $button = $('<button type="button" class="btn"><i class="icon-file icon-white"></i><span class="text">Choose file</span></button>');

            $fileInput.hide();
            $fileInput.after($button);

            $button.bind('click', function (e) {
                $fileInput.trigger('click');
            });

            $fileInput.bind('change', function (e) {
                var filename = this.value.split(/(\\|\/)/);
                $button.find('.text').text(filename[filename.length - 1]);
            });
        });

    });
})(jQuery);