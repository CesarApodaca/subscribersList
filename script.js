jQuery(document).ready(function($) {

    $(function() {
        $('form.sl_form').submit(function(event) {
            event.preventDefault(); // Prevent the form from submitting via the browser
            const form = $(this);
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize()
            }).done(function(data) {
                $('#successForm').removeClass("notVisible");
                $('#sl_form').addClass("notVisible");
            }).fail(function(data) {
            });
        });

        $('#successForm').click(function() {
            $('#successForm').addClass("notVisible");
        });
    });
});

