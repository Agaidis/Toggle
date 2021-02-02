$(document).ready(function () {

    let phoneTable = $('#owner_phone_table').DataTable({
        "pagingType": "simple",
        "pageLength": 10,
        "aaSorting": [],
    }).on('click', '.add_phone_btn', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');

        openPhoneModal(splitId[2], $('#interest_area').val());
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

    $('.phone_container').on('click', '.soft_delete_phone', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let uniqueId = splitId[2];

        deletePhone(uniqueId);

    }).on('click', '.push_back_phone', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let uniqueId = splitId[3];

        pushPhoneNumber(uniqueId);

    });

    $('.owner_submit_phone_btn').on('click', function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            beforeSend: function beforeSend(xhr) {
                xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));},
            type: "POST",
            url: '/lease-page/addPhone',
            data: {
                id: -1,
                ownerName: $('#owner_name').val(),
                interestArea: $('#interest_area').val(),
                phoneDesc: $('#new_phone_desc').val(),
                phoneNumber: $('#new_phone_number').val(),
                leaseName: $('#lease_name').val()
            },
            success: function success(data) {
                $('#new_phone_desc').val('').text('');
                $('#new_phone_number').val('').text('');
                let phoneNumber = '<span><div id="phone_' + data.id + '" style="padding: 2%;">' +
                    '<span style="font-weight: bold;">' + data.phone_desc + ': </span>' +
                    '<span><a href="tel:' + data.id + '">' + data.phone_number + ' </a></span>' +
                    '<span style="cursor:pointer; color:red; margin-left:5%;" class="soft_delete_phone fas fa-trash" id="soft_delete_' + data.id + '" "></span>' +
                    '<span style="cursor:pointer; color:darkorange; margin-left:5%;" class="push_back_phone fas fa-hand-point-right" id="push_back_phone_' + data.id + '" "></span>' +
                    '</div></span>';

                $('.phone_container').append($(phoneNumber).html());

                phoneTable.row.add( [
                    '<td class="text-center" style="font-weight:bold">' + data.phone_desc + '</td>',
                    '<td class="text-center"><a href="tel:' + data.phone_number + '">' + data.phone_number + '</a></td>'
                    ] ).draw( false );
            },
            error: function error(data) {
                console.log(data);
                $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
            }
        });
    });


});

function openPhoneModal(ownerName, interestArea) {
    $('#new_phone_desc').val('').text('');
    $('#new_phone_number').val('').text('');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
        },
        type: "GET",
        url: '/lease-page/getOwnerNumbers',
        data: {
            id: id,
            interestArea: interestArea,
            ownerName: ownerName
        },
        success: function success(data) {
            console.log(data);
            let phoneNumbers = '<div>';
            $.each(data, function (key, value) {
                phoneNumbers += '<span><div id="phone_'+value.id+'" style="padding: 2%;">'+
                    '<span style="font-weight: bold;">'+value.phone_desc+': </span>'+
                    '<span><a href="tel:'+value.id+'">'+value.phone_number+' </a></span>'+
                    '<span style="cursor:pointer; color:red; margin-left:5%;" class="soft_delete_phone fas fa-trash" id="soft_delete_'+value.id+'" "></span>'+
                    '<span style="cursor:pointer; color:darkorange; margin-left:5%;" class="push_back_phone fas fa-hand-point-right" id="push_back_phone_'+value.id+'" "></span>'+
                    '</div></span>';
            });
            phoneNumbers += '</div>';

            $('.phone_container').empty().append($(phoneNumbers).html());
        },
        error: function error(data) {
            console.log(data);
            $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
        }
    });
}

function deletePhone(id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $.ajax({
        beforeSend: function beforeSend(xhr) {
            xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));
        },
        type: "POST",
        url: '/lease-page/softDeletePhone',
        data: {
            id: id,
            interestArea: $('#interest_area').val(),

        },
        success: function success(data) {
            console.log(data);
            $('#phone_'+id).remove();

        },
        error: function error(data) {
            console.log(data);
            $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
        }
    });
}

function pushPhoneNumber(id) {
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
        url: '/lease-page/pushPhoneNumber',
        data: {
            id: id,
            reason: '',
            interestArea: $('#interest_area').val(),

        },
        success: function success(data) {
            $('#phone_'+id).remove();
        },
        error: function error(data) {
            console.log(data);
            $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
        }
    });
}