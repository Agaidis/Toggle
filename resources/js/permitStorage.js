$(document).ready(function () {

    $('#stored_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        sendPermitBack(permitId, leaseName);
    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];

        updatePermitStorageAssignee(assignee, permitId);
    });


    function sendPermitBack( permitId, leaseName ) {
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
            url: '/permit-storage/sendBack',
            data: {
                permitId: permitId,
                leaseName: leaseName
            },
            success: function success(data) {
                console.log(data);
                console.log(permitId);
                $('#permit_row_' + permitId).remove();

            },
            error: function error(data) {
                console.log(data);
                $('.notes').val('Note Submission Error. Make sure You Selected a Permit').text('Note Submission Error. Make sure You Selected a Permit');
            }
        });
    }

    function updatePermitStorageAssignee(assignee, permitId) {
        if (assignee === '') {
            $(this).removeClass('assigned_style');
        } else {
            $(this).addClass('assigned_style');
        }

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
            url: '/new-permits/updateAssignee',
            data: {
                permitId: permitId,
                assigneeId: assignee
            },
            success: function success(data) {

                console.log(data);
            },
            error: function error(data) {
                console.log(data);
            }
        });
    }
});