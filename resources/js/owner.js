$(document).ready(function () {

    let phoneTable = $('#owner_phone_table').DataTable({
        "pagingType": "simple",
        "pageLength": 10,
        "aaSorting": [],
    });

    let ownerLeaseTable = $('#owner_lease_table').DataTable( {
        "pagingType": "simple",
        "pageLength": 5,
        "aaSorting": [],
    });


    $('#email_btn').on('click', function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            beforeSend: function beforeSend(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
            },
            type: "PUT",
            url: '/owner/updateEmail',
            data: {
                email: $('#email').val(),
                name: $('#owner_name').val()
            },
            success: function success(data) {

                $('.status-msg').text('Email has successfully been Updated!').css('display', 'block');
                setTimeout(function () {
                    $('.status-msg').css('display', 'none');
                }, 2500);
            },
            error: function error(data) {
                console.log(data);
            }
        });
    });
});