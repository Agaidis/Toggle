$(document).ready(function () {

    $('#wellbore_user_change').on('change', function() {
        console.log($(this)[0].value);

        window.location.href = 'http://ltetoggle.com/wellbore/' + $(this)[0].value;
    });
});


