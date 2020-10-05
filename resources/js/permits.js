$(document).ready(function () {

    $('.interest_tab').on('click', function(){

        $('.interest_tab').removeClass('interest_active');
        let interestId = $(this)[0].id;

        $('#' + interestId).addClass('interest_active');

    });

    $('a[data-toggle="tab"]').on('click', function(e) {
        window.localStorage.setItem('activeTab', $(e.target).attr('href'));
    });

    let activeTab = window.localStorage.getItem('activeTab');

    if (activeTab) {
        $('#myTab a[href="' + activeTab + '"]').tab('show');
        window.localStorage.removeItem("activeTab");
    } else {
        $('#interest_tab_eagle').addClass('interest_active');
    }

    if (location.hash.substr(0,2) === "#!") {

        let interestHref = location.hash.replace('#!', '');

        if (interestHref === 'wtx_interest_area') {
            $('#interest_tab_wtx').addClass('interest_active');
            $('#interest_tab_etx').removeClass('interest_active');
            $('#interest_tab_eagle').removeClass('interest_active');
            $('#interest_tab_nm').removeClass('interest_active');
            $('#interest_tab_la').removeClass('interest_active');
        } else if (interestHref === 'etx_interest_area') {
            $('#interest_tab_wtx').removeClass('interest_active');
            $('#interest_tab_etx').addClass('interest_active');
            $('#interest_tab_eagle').removeClass('interest_active');
            $('#interest_tab_nm').removeClass('interest_active');
            $('#interest_tab_la').removeClass('interest_active');
        } else if (interestHref === 'eagle_interest_area') {
            $('#interest_tab_wtx').removeClass('interest_active');
            $('#interest_tab_etx').removeClass('interest_active');
            $('#interest_tab_eagle').addClass('interest_active');
            $('#interest_tab_nm').removeClass('interest_active');
            $('#interest_tab_la').removeClass('interest_active');
        } else if (interestHref === 'la_interest_area') {
            $('#interest_tab_wtx').removeClass('interest_active');
            $('#interest_tab_etx').removeClass('interest_active');
            $('#interest_tab_eagle').removeClass('interest_active');
            $('#interest_tab_nm').removeClass('interest_active');
            $('#interest_tab_la').addClass('interest_active');
        } else if (interestHref === 'nm_interest_area') {
            $('#interest_tab_wtx').removeClass('interest_active');
            $('#interest_tab_etx').removeClass('interest_active');
            $('#interest_tab_eagle').removeClass('interest_active');
            $('#interest_tab_nm').addClass('interest_active');
            $('#interest_tab_la').removeClass('interest_active');
        }

        $("a[href='#" + location.hash.substr(2) + "']").tab("show");
    }

    $("a[data-toggle='tab']").on("shown.bs.tab", function (e) {
        let hash = $(e.target).attr("href");
        console.log(hash);
        if (hash.substr(0,1) === "#") {
            location.replace("#!" + hash.substr(1));
        }
    });
    if (location.href.split('/')[3] === 'mm-platform') {
        getOilGasPrices();

    }

    let globalPermitId = '';

    //EAGLE PERMIT TABLE
    let eaglePermitTable = $('#eagle_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = eaglePermitTable.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, false);
    }).on('change', '.assignee', function() {
        updateAssignee($(this)[0].value);
    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');
        console.log(status);
        console.log(permitId[2]);

        toggleStatus(permitId[2], status);

    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        globalPermitId = permitId;
    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);
    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });


    //WTX PERMIT TABLE
    let wtxPermitTable = $('#wtx_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = wtxPermitTable.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, false)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });



    //ETX PERMIT TABLE
    let etxPermitTable = $('#etx_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = etxPermitTable.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, false)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });



    //NM PERMIT TABLE
    let laPermitTable = $('#la_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = laPermitTable.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, false)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });




    //NM PERMIT TABLE
    let nmPermitTable = $('#nm_permit_table').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nmPermitTable.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, false)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });



    // non Producing EAGLE TABLE
    let nonProducingEaglePermits = $('#non_producing_eagle_permits').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nonProducingEaglePermits.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, true)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('change', '.check_lease', function() {
        let id = $(this)[0].id;
        let isChecked = $(this)[0].checked;
        let splitId = id.split('_');
        let leaseId = splitId[2];
        let permitId = splitId[3];


        stitchLeaseToPermit(leaseId, permitId, isChecked);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });


    // non Producing WTX TABLE
    let nonProducingWTXPermits = $('#non_producing_wtx_permits').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nonProducingWTXPermits.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, true)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('change', '.check_lease', function() {
        let id = $(this)[0].id;
        let isChecked = $(this)[0].checked;
        let splitId = id.split('_');
        let leaseId = splitId[2];
        let permitId = splitId[3];


        stitchLeaseToPermit(leaseId, permitId, isChecked);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });



    // non Producing ETX TABLE
    let nonProducingETXPermits = $('#non_producing_etx_permits').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nonProducingETXPermits.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, true)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('change', '.check_lease', function() {
        let id = $(this)[0].id;
        let isChecked = $(this)[0].checked;
        let splitId = id.split('_');
        let leaseId = splitId[2];
        let permitId = splitId[3];


        stitchLeaseToPermit(leaseId, permitId, isChecked);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });



    // non Producing NM TABLE
    let nonProducingLAPermits = $('#non_producing_la_permits').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nonProducingLAPermits.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, true)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('change', '.check_lease', function() {
        let id = $(this)[0].id;
        let isChecked = $(this)[0].checked;
        let splitId = id.split('_');
        let leaseId = splitId[2];
        let permitId = splitId[3];


        stitchLeaseToPermit(leaseId, permitId, isChecked);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });




    // non Producing NM TABLE
    let nonProducingNMPermits = $('#non_producing_nm_permits').DataTable({
        "pagingType": "simple",
        "aaSorting": [],
        "stateSave": true,
        "order": [[ 2, "asc" ]]
    }).on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = nonProducingNMPermits.row( tr );

        moreData(id, tr, permitId, reportedOperator, row, true)

    }).on('change', '.assignee', function() {
        let assignee = $(this)[0].value;

        updateAssignee(assignee);

    }).on('change', '.toggle_status', function() {
        let id = $(this)[0].id;
        let status = $(this)[0].value;
        let permitId = id.split('_');

        toggleStatus(permitId[2], status );
    }).on('click', '.permit_row', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        globalPermitId = splitId[2];

    }).on('click', '.update_permit_notes_btn', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[3];

        updateNotes(permitId);

    }).on('mouseover', '.permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[1];
        let permitId = splitId[2];

        $('#' + id).css('background-color', 'lightgrey');
        $('#delete_permit_note_'+noteId+'_'+permitId).css('display', 'inherit');
    }).on('mouseleave', '.permit_note', function() {
        $('.delete_permit_note').css('display', 'none');
        $('.permit_note').css('background-color', '#F2EDD7FF');
    }).on('click', '.delete_permit_note', function() {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let noteId = splitId[3];
        let permitId = splitId[4];
        let response = confirm('Are you sure you want to delete this note?');

        deleteNote(permitId, noteId, response);
    }).on('change', '.check_lease', function() {
        let id = $(this)[0].id;
        let isChecked = $(this)[0].checked;
        let splitId = id.split('_');
        let leaseId = splitId[2];
        let permitId = splitId[3];


        stitchLeaseToPermit(leaseId, permitId, isChecked);
    }).on('click', '.store_button', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[2];
        let leaseName = splitId[3];

        storePermit(permitId, leaseName);
    });


    function moreData(id, tr, permitId, reportedOperator, row, isNonProducing) {
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
            url: '/new-permits/getPermitDetails',
            data: {
                permitId: permitId,
                reportedOperator: reportedOperator,
                isNonProducing: isNonProducing
            },
            success: function success(data) {
                let permitBody = '<div class="row"><div class="col-md-6">' +
                    '<h3>Location & Contact</h3>' +
                    '<div class="containers">' +
                    '<label for="permit_number_'+ permitId+'">Permit Number: </label>' +
                    '<span id="permit_number_'+ permitId+'"></span><br>' +
                    '<label for="County/Parish_'+ permitId+'">County State: </label>' +
                    '<span id="CountyParish_'+ permitId+'"></span><br>' +
                    '<label for="Township_'+ permitId+'">Township: </label>' +
                    '<span id="Township_'+ permitId+'"></span><br>' +
                    '<label for="OperatorAlias_'+ permitId+'">Operator: </label>' +
                    '<span id="OperatorAlias_'+ permitId+'"></span><br>' +
                    '<label for="area_acres_'+ permitId+'">Acreage: </label>' +
                    '<span id="area_acres_'+ permitId+'"></span><br>' +
                    '<label for="Range_'+ permitId+'">Range: </label>' +
                    '<span id="Range_'+ permitId+'"></span><br>' +
                    '<label for="Section_'+ permitId+'">Section: </label>' +
                    '<span id="Section_'+ permitId+'"></span><br>' +
                    '<label for="District_'+ permitId+'">District: </label>' +
                    '<span id="District_'+ permitId+'"></span><br>' +
                    '<label for="Block_'+ permitId+'">Block: </label>' +
                    '<span id="Block_'+ permitId+'"></span>' +
                    '</div></div>' +
                    '<div class="col-md-6">' +
                    '<h3>Permit Info.</h3>' +
                    '<div class="containers">' +
                    '<label for="permitStatus_'+ permitId+'">Permit Status:</label>' +
                    '<span id="permitStatus_'+ permitId+'"></span><br>' +
                    '<label for="DrillType_'+ permitId+'">Drill Type: </label>' +
                    '<span id="DrillType_'+ permitId+'"></span><br>' +
                    '<label for="PermitType_'+ permitId+'">Permit Type: </label>' +
                    '<span id="PermitType_'+ permitId+'"></span><br>' +
                    '<label for="WellType_'+ permitId+'">Well Type: </label>' +
                    '<span id="WellType_'+ permitId+'"></span><br>' +
                    '<label for="approved_date_'+ permitId+'">Approved Date: </label>' +
                    '<span id="ApprovedDate_'+ permitId+'"></span><br>' +
                    '<label for="submitted_date_'+ permitId+'">Submitted Date: </label>' +
                    '<span id="SubmittedDate_'+ permitId+'"></span><br>' +
                    '<label for="Survey_'+ permitId+'">Survey: </label>' +
                    '<span id="Survey_'+ permitId+'"></span><br>' +
                    '<label for="Abstract_'+ permitId+'">Abstract: </label>' +
                    '<span id="Abstract_'+ permitId+'"></span><br>' +
                    '</div></div></div><br>' +
                    '<div class="row"><div class="col-md-6">' +
                    '<div class="map" id="map_'+ permitId+'"></div></div>' +
                    '<div class="col-md-6"><div style="margin-top:1.5%;">' +
                    '<label style="font-size:20px; font-weight:bold;" for="previous_notes">Previous Landtrac Notes</label>' +
                    '<div class="previous_notes" name="previous_notes" id="previous_notes_'+ permitId+'" contenteditable="false"></div><br>' +
                    '<label style="font-size:20px; font-weight:bold;" for="notes_'+ permitId+'">Submit Landtrac Notes</label><br>' +
                    '<textarea rows="5" class="notes" name="notes" id="notes_'+ permitId+'" style="width:300px;" placeholder="Enter Notes: "></textarea><br>' +
                    '<button type="button" id="update_notes_btn_'+ permitId+'" class="btn btn-primary update_permit_notes_btn">Update Notes</button>' +
                    '</div></div></div>';

                getNotes(id, permitId);
                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child( permitBody ).show();
                    tr.addClass('shown');

                    try {
                        let centerSurfaceLng = '{"lng":' + data.allRelatedPermits[0].SurfaceLongitudeWGS84;
                        let centerSurfaceLat = '"lat":' + data.allRelatedPermits[0].SurfaceLatitudeWGS84 + '}';
                        let map;
                        let bounds = new google.maps.LatLngBounds();

                        map = new google.maps.Map(document.getElementById('map_' + permitId), {
                            zoom: 15,
                            center: JSON.parse(centerSurfaceLng + ',' + centerSurfaceLat),
                            mapTypeId: google.maps.MapTypeId.HYBRID
                        });

                        $.each(data.allRelatedPermits, function (key, value) {
                            let surfaceLng = '{"lng":' + value.SurfaceLongitudeWGS84;
                            let surfaceLat = '"lat":' + value.SurfaceLatitudeWGS84 + '}';
                            let btmGeo = value.btm_geometry.replace(/\s/g, '').replace(/},/g, '},dd').replace('(', '').replace(')', '').split(',dd');
                            let position = new google.maps.LatLng(JSON.parse(surfaceLng + ',' + surfaceLat));

                            bounds.extend(position);

                            let btmPosition = new google.maps.LatLng(JSON.parse(btmGeo));
                            bounds.extend(btmPosition);

                            let flightPath = new google.maps.Polyline({
                                path: [
                                    JSON.parse(surfaceLng + ',' + surfaceLat),
                                    JSON.parse(btmGeo)
                                ],
                                geodesic: true,
                                strokeColor: "#ab0000",
                                strokeOpacity: 1.0,
                                strokeWeight: 2
                            });

                            let permitMarker = new google.maps.Marker({
                                position: position,
                                map: map,
                                label: 'BM',
                                title: value.lease_name
                            });

                            let SurfaceMarker = new google.maps.Marker({
                                position: btmPosition,
                                map: map,
                                label: 'SF',
                                title: value.lease_name
                            });

                            flightPath.setMap(map);
                            google.maps.event.addListener(SurfaceMarker, 'click', (function(SurfaceMarker) {
                                return function() {
                                    infoWindow.setContent('<div class="info_content">' +
                                        '<h4>Lease: '+value.lease_name+'</h4>' +
                                        '<h5>Well Type: '+value.well_type+'</h5>' +
                                        '<h5>Survey: '+value.survey+'</h5>' +
                                        '<h5>Range: '+value.range+'</h5>' +
                                        '<h5>Section: '+value.section+'</h5>' +
                                        '<h5>Township: '+value.township+'</h5>' +
                                        '</div>');
                                    infoWindow.open(map, SurfaceMarker);
                                }
                            })(SurfaceMarker));

                            google.maps.event.addListener(permitMarker, 'click', (function(permitMarker) {
                                return function() {
                                    infoWindow.setContent('<div class="info_content">' +
                                        '<h4>Lease: '+value.lease_name+'</h4>' +
                                        '<h5>Well Type: '+value.well_type+'</h5>' +
                                        '<h5>Survey: '+value.survey+'</h5>' +
                                        '<h5>Range: '+value.range+'</h5>' +
                                        '<h5>Section: '+value.section+'</h5>' +
                                        '<h5>Township: '+value.township+'</h5>' +
                                        '</div>');
                                    infoWindow.open(map, permitMarker);
                                }
                            })(permitMarker));

                        });



                        // let permitPoint = data.permit.btm_geometry.replace(/\s/g, '').replace(/},/g, '},dd').replace('(', '').replace(')', '').split(',dd');
                        // let surfaceLng = '{"lng":' + data.permit.SurfaceLongitudeWGS84;
                        // let surfaceLat = '"lat":' + data.permit.SurfaceLatitudeWGS84 + '}';
                        //
                        //
                        // // Display a map on the page
                        // map = new google.maps.Map(document.getElementById('map_' + permitId), {
                        //     zoom: 13,
                        //     center: JSON.parse(surfaceLng + ',' + surfaceLat),
                        //     mapTypeId: google.maps.MapTypeId.HYBRID
                        // });
                        //
                        // let position = new google.maps.LatLng(JSON.parse(surfaceLng + ',' + surfaceLat));
                        // bounds.extend(position);




                        // Display multiple markers on a map
                        let infoWindow = new google.maps.InfoWindow(), marker;

                        // Loop through our array of markers & place each one on the map
                        $.each( data.leaseGeo, function (key, value ) {

                            let checkbox = '';

                            if (value.permit_stitch_id === permitId) {
                                checkbox = '<input type="checkbox" checked class="form-control check_lease" id="check_lease_'+value.LeaseId+'_'+permitId+'"/>';

                                 let icon = {
                                     url: "https://quickevict.nyc3.digitaloceanspaces.com/black%20icon.png",
                                     scaledSize: new google.maps.Size(30, 45),
                                 }

                                let position = new google.maps.LatLng(JSON.parse(value.Geometry));
                                bounds.extend(position);
                                marker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    title: value.Grantor,
                                    icon: icon
                                });
                            } else {
                                checkbox = '<input type="checkbox" class="form-control check_lease" id="check_lease_'+value.LeaseId+'_'+permitId+'"/>';
                                let position = new google.maps.LatLng(JSON.parse(value.Geometry));
                                bounds.extend(position);
                                marker = new google.maps.Marker({
                                    position: position,
                                    map: map,
                                    title: value.Grantor
                                });
                            }





                            // Allow each marker to have an info window
                            google.maps.event.addListener(marker, 'click', (function(marker) {
                                return function() {
                                    infoWindow.setContent('<div class="info_content">' +
                                        '<h4>Grantor: '+value.Grantor+'</h4>' +
                                        '<h5>Range: '+value.Range+'</h5>' +
                                        '<h5>Section: '+value.Section+'</h5>' +
                                        '<h5>Township: '+value.Township+'</h5>' +
                                        '</div><div> <label style="margin-left:40%;" for="check_lease">Add Lease to Permit: </label>'+checkbox+'</div>');
                                    infoWindow.open(map, marker);
                                }
                            })(marker));
                        });
                    } catch( err ) {
                        console.log(err);
                    }
                }

                let survey = data['permit']['survey'];
                if (data['permit']['survey'] === null) {
                    survey = 'N/A';
                } else {
                    survey = data['permit']['survey'];
                }

                let abstract = data['permit']['abstract'];
                if (data['permit']['abstract'] === null) {
                    abstract = 'N/A';
                } else {
                    abstract = data['permit']['abstract'];
                }

                let district = data['permit']['district'];
                if (data['permit']['district'] === null) {
                    district = 'N/A';
                } else {
                    district = data['permit']['district'];
                }

                let block = data['permit']['block'];
                if (data['permit']['block'] === null) {
                    block = 'N/A';
                } else {
                    block = data['permit']['block'];
                }

                let approvedDate = '';
                if (data['permit']['approved_date'] !== null) {
                    approvedDate = data['permit']['approved_date'].split('T');
                } else {
                    approvedDate = 'N/A';
                }

                let permitStatus = '';
                if (data['permit']['permit_status'] !== null) {
                    permitStatus = data['permit']['permit_status'].split('T');
                } else {
                    permitStatus = 'N/A';
                }

                let submittedDate = '';
                if (data['permit']['submitted_date'] !== null) {
                    submittedDate = data['permit']['submitted_date'].split('T');
                } else {
                    submittedDate = 'N/A';
                }

                let township = '';
                if (data['permit']['submitted_date'] !== null) {
                    township = data['permit']['township'];
                } else {
                    township = 'N/A';
                }

                let acreage = '';
                if (data['permit']['acreage'] !== null) {
                    acreage = data['permit']['acreage'];
                } else {
                    acreage = 'N/A';
                }

                let drillType = '';
                if (data['permit']['drill_type'] !== null) {
                    drillType = data['permit']['drill_type'];
                } else {
                    drillType = 'N/A';
                }

                let leaseName = '';
                if (data['permit']['lease_name'] !== null) {
                    leaseName = data['permit']['lease_name'];
                } else {
                    leaseName = 'N/A';
                }

                let range = '';
                if (data['permit']['range'] !== null) {
                    range = data['permit']['range'];
                } else {
                    range = 'N/A';
                }

                let section = '';
                if (data['permit']['section'] !== null) {
                    section = data['permit']['section'];
                } else {
                    section = 'N/A';
                }

                let wellType = '';
                if (data['permit']['well_type'] !== null) {
                    wellType = data['permit']['well_type'];
                } else {
                    wellType = 'N/A';
                }

                $('#Abstract_'+ permitId).text(' ' + abstract);
                $('#ApprovedDate_'+ permitId).text(' ' + approvedDate[0]);
                $('#SubmittedDate_'+ permitId).text(' ' + submittedDate[0]);
                $('#Block_'+ permitId).text(' ' + block);
                $('#permitStatus_'+ permitId).text(' ' + permitStatus);
                $('#CountyParish_'+ permitId).text(' ' + data['permit']['county_parish'] + ', ' + data['permit']['state']);
                $('#DrillType_'+ permitId).text(' ' + drillType);
                $('#LeaseName_'+ permitId).text(' ' + leaseName);
                $('#OperatorAlias_'+ permitId).text(' ' + data['permit']['operator_alias']);
                $('#PermitID_'+ permitId).text(' ' + data['permit']['permit_id']);
                $('#PermitType_'+ permitId).text(' ' + data['permit']['permit_type']);
                $('#permit_number_'+ permitId).text(' ' + data['permit']['permit_number']);
                $('#Range_'+ permitId).text(' ' + range);
                $('#Section_'+ permitId).text(' ' + section);
                $('#Survey_'+ permitId).text(' ' + survey);
                $('#Township_'+ permitId).text(' ' + township);
                $('#WellType_'+ permitId).text(' ' + wellType);
                $('#area_acres_'+ permitId).text(' ' + acreage);
                $('#District_'+ permitId).text(' ' + district);


            },
            error: function error(data) {
                console.log(data);
            }
        });
    }

    function stitchLeaseToPermit(leaseId, permitId, isChecked) {
        console.log(isChecked);
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
            url: '/new-permits/stitchLeaseToPermit',
            data: {
                permitId: permitId,
                leaseId: leaseId,
                isChecked: isChecked
            },
            success: function success(data) {
                if (data === 'success') {
                    $('.status-msg').text('Lease has been updated!').css('display', 'block');
                    setTimeout(function () {
                        $('.status-msg').css('display', 'none');
                    }, 2500);
                }
                console.log(data);
            },
            error: function error(data) {
                console.log(data);
            }
        });
    }

    function updateAssignee(assignee) {
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
                permitId: globalPermitId,
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

    function getOilGasPrices() {
        let oilPrice = $('#price_container')[0].children[0].children[0].children[2].firstElementChild.innerText;
        let gasPrice = $('#price_container')[0].children[0].children[1].children[2].firstElementChild.innerText;
        oilPrice = oilPrice.replace('$', '');
        gasPrice = gasPrice.replace('$', '');

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
            url: '/update-prices',
            data: {
                oilPrice: oilPrice,
                gasPrice: gasPrice
            },
            success: function success(data) {
            },
            error: function error(data) {
            }
        });
    }

    function toggleStatus( permitId, status ) {

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
            url: '/new-permits/updateStatus',
            data: {
                permitId: permitId,
                status: status
            },
            success: function success(data) {

                $('#toggle_status_' + permitId).removeClass('yellow').removeClass('purple').removeClass('blue').removeClass('green').removeClass('red').addClass(data);

            },
            error: function error(data) {
                console.log(data);
            }
        });
    }

    function getNotes( id, permitId ) {
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
            url: '/new-permits/getNotes',
            data: {
                permitId: permitId
            },
            success: function success(data) {
                if (data !== undefined && data !== '') {
                    let updatedNotes = '';

                    $.each(data, function (key, value) {
                        updatedNotes += '<span>'+value.notes+'</span>';
                    });
                    updatedNotes = $('<span>' + updatedNotes + '</span>');

                    $('#previous_notes_' + permitId).empty().append(updatedNotes.html());
                } else {
                    $('#previous_notes_' + permitId).empty();
                }
            },
            error: function error(data) {
                console.log(data);
                $('.notes').val('Note Submission Error. Make sure You Selected a Permit').text('Note Submission Error. Make sure You Selected a Permit');
            }
        });
    }

    function storePermit( permitId, leaseName ) {
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
            url: '/new-permits/storePermit',
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

            }
        });

    }

    function updateNotes( permitId ) {
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
            url: '/new-permits/updateNotes',
            data: {
                permitId: permitId,
                notes: $('#notes_' + permitId).val(),

            },
            success: function success(data) {
                let updatedNotes = '';

                $.each(data, function (key, value) {
                    updatedNotes += '<span>'+value.notes+'</span>';
                });
                updatedNotes = $('<span>' + updatedNotes + '</span>');

                $('#previous_notes_' + permitId).empty().append(updatedNotes.html());
                $('#notes_' + permitId).val('').text('');
            },
            error: function error(data) {
                $('#notes_' + permitId).val('Note Submission Error. Make sure You Selected a Permit').text('Note Submission Error. Make sure You Selected a Permit');
            }
        });
    }

    function deleteNote( permitId, noteId, response ) {
        if (response) {
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
                url: '/new-permits/delete/delete-note',
                data: {
                    id: noteId
                },
                success: function success(data) {
                    console.log(data);
                    let updatedNotes = '';

                    $.each(data, function (key, value) {
                        updatedNotes += '<span>'+value.notes+'</span>';
                    });
                    updatedNotes = $('<span>' + updatedNotes + '</span>');

                    $('#previous_notes_' + permitId).empty().append(updatedNotes.html());
                },
                error: function error(data) {
                    console.log(data);
                }
            });
        }
    }
});