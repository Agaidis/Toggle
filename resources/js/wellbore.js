$(document).ready(function () {

    $('#wellbore_user_change').on('change', function() {
        console.log($(this)[0].value);

        window.location.href = 'https://ltetoggle.com/wellbore/' + $(this)[0].value;
    });
});


