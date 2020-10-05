 $(document).ready(function () {

     $('#create_lease_name_select').select2({
         multiple: true,
         minimumInputLength: 3
     });

     if (location.href.split('/')[3] === 'lease-page') {


         $('#well_name_select').select2({
             multiple: true,
             minimumInputLength: 3
         });

         if ($('#well_name_select option')[0] !== undefined) {
             $('#well_name_select option')[0].remove();
         }


         if ($('#interest_area').val() !== 'nm' && $('#interest_area').val() !== 'la') {
             $('#lease_name_select').select2({
                 multiple: true,
                 minimumInputLength: 3
             });
             $('#lease_name_select option')[0].remove();
         }

     let globalOwnerId = '';

     let map;
     let bounds = new google.maps.LatLngBounds();

     if (toggle.allRelatedPermits !== undefined && toggle.allRelatedPermits !== 'undefined' && toggle.allRelatedPermits.length !== 0) {
         let surfaceLng = '{"lng":' + toggle.allRelatedPermits[0].SurfaceLongitudeWGS84;
         let surfaceLat = '"lat":' + toggle.allRelatedPermits[0].SurfaceLatitudeWGS84 + '}';

         map = new google.maps.Map(document.getElementById('proMap'), {
             zoom: 15,
             center: JSON.parse(surfaceLng + ',' + surfaceLat),
             mapTypeId: google.maps.MapTypeId.HYBRID
         });

         $.each(toggle.allRelatedPermits, function (key, value) {
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

             flightPath.setMap(map);
         });

     } else {

         if (toggle.allWells[0] !== undefined) {
             let surfaceLng = '{"lng":' + toggle.allWells[0].SurfaceHoleLongitudeWGS84;
             let surfaceLat = '"lat":' + toggle.allWells[0].SurfaceHoleLatitudeWGS84 + '}';

             map = new google.maps.Map(document.getElementById('proMap'), {
                 zoom: 13,
                 center: JSON.parse(surfaceLng + ',' + surfaceLat),
                 mapTypeId: google.maps.MapTypeId.HYBRID
             });


         } else {
             map = new google.maps.Map(document.getElementById('proMap'), {
                 zoom: 13,
                 center: JSON.parse('{"lng":-101.4401672,"lat":32.957712}'),
                 mapTypeId: google.maps.MapTypeId.HYBRID
             });
         }
     }

     // Display multiple markers on a map
     let infoWindow = new google.maps.InfoWindow(), marker;

     // Loop through our array of markers & place each one on the map
     $.each(toggle.allWells, function (key, value) {

         let surfaceLng = '{"lng":' + value.SurfaceHoleLongitudeWGS84;
         let surfaceLat = '"lat":' + value.SurfaceHoleLatitudeWGS84 + '}';
         let icon = '';

         if (value.stitched_permit_id === toggle.permitId) {
             icon = 'https://quickevict.nyc3.digitaloceanspaces.com/background1.jpg';
         } else if (toggle.selectedWells.includes(value.WellName)) {
             icon = 'https://quickevict.nyc3.digitaloceanspaces.com/blackWell.png';
         } else {
             icon = 'https://quickevict.nyc3.digitaloceanspaces.com/wellIcon.png';
         }

         let position = new google.maps.LatLng(JSON.parse(surfaceLng + ',' + surfaceLat));
         bounds.extend(position);
         marker = new google.maps.Marker({
             position: position,
             map: map,
             title: value.Grantor,
             icon: icon
         });

         // Allow each marker to have an info window
         google.maps.event.addListener(marker, 'click', (function (marker) {
             return function () {
                 infoWindow.setContent('<div class="info_content">' +
                     '<h4>Well Name: ' + value.WellName + '</h4>' +
                     '<h4>Well Number: ' + value.WellNumber + '</h4>' +
                     '<h5>Status: ' + value.WellStatus + '</h5>' +
                     '<h5>Drill Type: ' + value.DrillType + '</h5>' +
                     '<h5>Depth: ' + value.MeasuredDepth + '</h5>' +
                     '</div>');
                 infoWindow.open(map, marker);
             }
         })(marker));
     });

     $('.previous_notes').html($('#hidden_permit_notes').val());

     $('.acreage').on('focusout', function() {
         let id = $(this)[0].id;
         let splitId = id.split('_');
         let uniqueId = splitId[1];
         setAcreage(uniqueId, $('.acreage').val());
     });

     $('#refresh_well_data_btn').on('click', function() {
         updateWellData();
     });

     $('#refresh_lease_data_btn').on('click', function() {
         updateLeaseNames();
     });

     $('.owner_follow_up').datepicker().on('change', function() {
         let id = $(this)[0].id;
         let splitId = id.split('_');
         let uniqueId = splitId[3];
         let date = $('#owner_follow_up_' + uniqueId).val();

        updateFollowUp(uniqueId, date, $('#interest_area').val());
     });

        let ownerTable = $('.owner_table').DataTable({
            "pagingType": "simple",
            "pageLength" : 25,
            "aaSorting": [],
            "order": [[ 6, "desc" ]]
        }).on('change', '.owner_assignee', function() {
            let id = $(this)[0].id;
            let assignee = $(this)[0].value;
            let ownerId = id.split('_');

            updateAssignee(ownerId[1], assignee, $('#interest_area').val());

        }).on('click', '.owner_row', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            globalOwnerId = splitId[2];

        }).on('click', 'td.owner-details-control', function () {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[1];
            globalOwnerId = ownerId;
            let tr = $(this).closest('tr');
            let row = ownerTable.row( tr );

            openOwnerPanel(ownerId, tr, row);

        }).on('change', '.wellbore_dropdown', function () {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[2];
            let wellType = $(this)[0].value;




            updateWellbore(ownerId, wellType, $('#interest_area').val());
        }).on('focusout', '.owner_price', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[2];

           updatePrice(ownerId);
        }).on('click', '.update_owner_notes_btn', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[4];

           updateNotes(ownerId, $('#lease_name').val(), $('#interest_area').val());
        }).on('mouseover', '.owner_note', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let noteId = splitId[1];
            let ownerId = splitId[2];

            $('#' + id).css('background-color', 'lightgrey');
            $('#delete_owner_note_'+noteId+'_'+ownerId).css('display', 'inherit');
        }).on('mouseleave', '.owner_note', function() {
            $('.delete_owner_note').css('display', 'none');
            $('.owner_note').css('background-color', '#F2EDD7FF');
        }).on('click', '.delete_owner_note', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let noteId = splitId[3];
            let ownerId = splitId[4];

            deleteNote(ownerId, noteId);
        }).on('click', '.open_phone_modal', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[2];

            openPhoneModal(ownerId, $('#interest_area').val());
        }).on('change', '.change_product', function() {
            let id = $(this)[0].id;
            let splitId = id.split('_');
            let ownerId = splitId[2];
            changeProduct(ownerId, $(this).val());
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

        $('.submit_phone_btn').on('click', function () {
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
                    id: globalOwnerId,
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
                    },
                error: function error(data) {
                    console.log(data);
                    $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
                }
            });
        });


        /*              WELLS PRODUCTION DETAILS              */
        let table = $('.wells_table').DataTable();

        // Add event listener for opening and closing details
        $('.wells_table tbody').on('click', 'td.details-control', function () {
            let tr = $(this).closest('tr');
            let row = table.row( tr );

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
                url: '/lease-page/getWellDetails',
                data: {
                    id: $(this)[0].id,
                    leaseName: $('#lease_name').val()
                },
                success: function success(data) {
                    let tableBody = '<table class="table table-bordered table-hover" style="padding-left:50px;"><thead>' +
                        '<tr>' +
                        '<th class="text-center">Cum Gas</th>' +
                        '<th class="text-center">Cum Oil</th>' +
                        '<th class="text-center">Measured Depth</th>' +
                        '<th class="text-center">Abstract</th>' +
                        '<th class="text-center">Range</th>' +
                        '<th class="text-center">District</th>' +
                        '<th class="text-center">Section</th>' +
                        '<th class="text-center">Prod Date</th>' +
                        '</tr>' +
                        '</thead>';
                    let tableRows = '';

                    $.each(data, function( key, value ) {
                        let firstProdDate = '';
                        let prodDate = '';

                        if (value.FirstProdDate !== null) {
                            prodDate = value.FirstProdDate;
                            let prodDateArray = prodDate.split('T');
                            firstProdDate = prodDateArray[0];
                        } else {
                            firstProdDate = 'N/A';
                        }

                        tableRows += '<tr>' +
                            '<td class="text-center">'+value.CumGas+'</td>' +
                            '<td class="text-center">'+value.CumOil+'</td>' +
                            '<td class="text-center">'+value.MeasuredDepth+'</td>' +
                            '<td class="text-center">'+value.Abstract+'</td>' +
                            '<td class="text-center">'+value.Range+'</td>' +
                            '<td class="text-center">'+value.District+'</td>' +
                            '<td class="text-center">'+value.Section+'</td>' +
                            '<td class="text-center">'+firstProdDate+'</td>' +
                            '</tr>'
                    });
                    tableBody += tableRows + '</table>';

                    if ( row.child.isShown() ) {
                        // This row is already open - close it
                        row.child.hide();
                        tr.removeClass('shown');
                    }
                    else {
                        // Open this row
                        row.child( tableBody ).show();
                        tr.addClass('shown');
                    }
                },
                error: function error(data) {
                    console.log(data);
                }
            });
        });

     };




     /*                          WELLBORE            PAGE                    */
     $(document).ready(function () {
         console.log(location.href.split('/')[3]);
         if (location.href.split('/')[3] === 'wellbore' || location.href.split('/')[3] === 'wellbore#!texas_area' || location.href.split('/')[3] === 'wellbore#!new_mexico_area' || location.href.split('/')[3] === 'wellbore#!louisiana_area') {



             // $('.navbar-nav .nav-link').click(function(){
             //     $('.navbar-nav .nav-link').removeClass('active');
             //     $(this).addClass('active');
             // })
             /* HIGH PRIORITY WELLBORE TX TABLE */

             $('.wellbore_owner_follow_up').datepicker();

             let highPriorityTable = $('.high_priority_wellbore_table').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(ownerId[1], assignee, 'tx');


             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'tx');


             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#current_interest_area').val('tx');
                 $('#owner_id').val(splitId[2]);

                 openPhoneModal(splitId[2], 'tx');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();


                 updateFollowUp(uniqueId, date, 'tx');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = highPriorityTable.row(tr);
                 getNotes(ownerId, tr, row, 'tx')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'tx');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)
             });

             /* LOWER PRIORITY WELLBORE TX TABLE */

             let lowPriorityTable = $('.low_priority_wellbore_table').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(assignee, ownerId[1], 'tx');

             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'tx');

             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);
                 $('#current_interest_area').val('tx');

                 openPhoneModal(splitId[2], 'tx');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();

                 updateFollowUp(uniqueId, date, 'tx');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = lowPriorityTable.row(tr);

                 getNotes(ownerId, tr, row, 'tx')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'tx');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)

             });


             /* HIGH PRIORITY WELLBORE NM TABLE */

             let highPriorityTableNM = $('.high_priority_wellbore_tableNM').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(ownerId[1], assignee, 'nm');


             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'nm');


             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);
                 $('#current_interest_area').val('nm');

                 openPhoneModal(splitId[2], 'nm');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();


                 updateFollowUp(uniqueId, date, 'nm');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = highPriorityTableNM.row(tr);
                 getNotes(ownerId, tr, row, 'nm')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'nm');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)
             });

             /* LOWER PRIORITY WELLBORE NM TABLE */

             let lowPriorityTableNM = $('.low_priority_wellbore_tableNM').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(assignee, ownerId[1], 'nm');

             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'nm');

             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);
                 $('#current_interest_area').val('nm');


                 openPhoneModal(splitId[2], 'nm');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();

                 updateFollowUp(uniqueId, date, 'nm');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = lowPriorityTableNM.row(tr);

                 getNotes(ownerId, tr, row, 'nm')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'nm');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)

             });


             /* HIGH PRIORITY WELLBORE LA TABLE */


             let highPriorityTableLA = $('.high_priority_wellbore_tableLA').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(ownerId[1], assignee, 'la');


             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'la');


             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);
                 $('#current_interest_area').val('la');

                 openPhoneModal(splitId[2], 'la');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();


                 updateFollowUp(uniqueId, date, 'la');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = highPriorityTableLA.row(tr);
                 getNotes(ownerId, tr, row, 'la')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'la');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)
             });

             /* LOWER PRIORITY WELLBORE LA TABLE */

             let lowPriorityTableLA = $('.low_priority_wellbore_tableLA').DataTable({
                 "pagingType": "simple",
                 "pageLength": 25,
                 "aaSorting": [],
                 "order": [[1, "desc"]]
             }).on('change', '.owner_assignee', function () {
                 let id = $(this)[0].id;
                 let assignee = $(this)[0].value;
                 let ownerId = id.split('_');

                 updateAssignee(assignee, ownerId[1], 'la');

             }).on('click', '.owner_row', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);

             }).on('change', '.wellbore_dropdown', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let wellType = $(this)[0].value;
                 $('#owner_id').val(splitId[2]);

                 updateWellbore(splitId[2], wellType, 'la');

             }).on('click', '.add_phone_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 $('#owner_id').val(splitId[2]);
                 $('#current_interest_area').val('la');


                 openPhoneModal(splitId[2], 'la');
             }).on('change', '.wellbore_owner_follow_up', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let uniqueId = splitId[3];
                 let date = $('#owner_follow_up_' + uniqueId).val();

                 updateFollowUp(uniqueId, date, 'la');
             }).on('click', 'td.wellbore-details-control', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[1];
                 let tr = $(this).closest('tr');
                 let row = lowPriorityTableLA.row(tr);

                 getNotes(ownerId, tr, row, 'la')

             }).on('click', '.update_owner_notes_btn', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let ownerId = splitId[4];

                 updateNotes(ownerId, $('#lease_name_' + ownerId).val(), 'la');
             }).on('mouseover', '.owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[1];
                 let ownerId = splitId[2];

                 $('#' + id).css('background-color', 'lightgrey');
                 $('#delete_owner_note_' + noteId + '_' + ownerId).css('display', 'inherit');
             }).on('mouseleave', '.owner_note', function () {
                 $('.delete_owner_note').css('display', 'none');
                 $('.owner_note').css('background-color', '#F2EDD7FF');
             }).on('click', '.delete_owner_note', function () {
                 let id = $(this)[0].id;
                 let splitId = id.split('_');
                 let noteId = splitId[3];
                 let ownerId = splitId[4];

                 deleteNote(ownerId, noteId)

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

             $('.wellbore_submit_phone_btn').on('click', function () {
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
                         id: $('#owner_id').val(),
                         interestArea: $('#current_interest_area').val(),
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
                     },
                     error: function error(data) {
                         console.log(data);
                         $('.owner_notes').val('Note Submission Error. Contact Dev Team').text('Note Submission Error. Contact Dev Team');
                     }
                 });
             });
         }
     });












     /*              FUNCTIONS               */

     function getNotes(ownerId, tr, row, interestArea) {
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
             url: '/lease-page/getNotes',
             data: {
                 ownerId: ownerId,
                 leaseName: $('#lease_name_' + ownerId).val(),
                 interestArea: interestArea
             },
             success: function success(data) {
                 console.log(data);
                 let noteContainer = '<div class="col-md-6">' +
                     '<div style="text-align:center;" class="col-md-12">' +
                     '<label style="font-size:20px; font-weight:bold;" for="notes">Owner Notes</label>' +
                     '<div class="previous_owner_notes" id="previous_owner_notes_' + ownerId + '" name="previous_owner_notes" contenteditable="false"></div>' +
                     '</div>' +
                     '<div style="text-align:center;" class="col-md-12">' +
                     '<label style="font-size:20px; font-weight:bold;" for="owner_notes_' + ownerId + '">Enter Owner Notes</label>' +
                     '<textarea rows="4" class="owner_notes" id="owner_notes_' + ownerId + '" name="notes" style="width:100%;" placeholder="Enter Notes: "></textarea>' +
                     '<div class="col-md-12">' +
                     '<button type="button" id="update_owner_notes_btn_' + ownerId + '" class="btn btn-primary update_owner_notes_btn">Update Notes</button>' +
                     '</div></div></div>';
                 if (row.child.isShown()) {
                     row.child.hide();
                     tr.removeClass('shown');
                 } else {
                     row.child(noteContainer).show();
                     tr.addClass('shown');
                 }
                 if (data !== undefined && data !== '') {
                     let updatedNotes = '';
                     $.each(data, function (key, value) {
                         updatedNotes += '<span>' + value.notes + '</span>';
                     });
                     updatedNotes = $('<span>' + updatedNotes + '</span>');
                     $('#previous_owner_notes_' + ownerId).empty().append(updatedNotes.html());
                 } else {
                     $('#previous_owner_notes_' + ownerId).empty();
                 }
                 },
             error: function error(data) {
                 $('.owner_notes').val('Note Submission Error. Make sure You Selected an Owner').text('Note Submission Error. Make sure You Selected an Owner');
             }
         });
     }

     function setAcreage(id, acreage) {
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
             url: '/lease-page/updateAcreage',
             data: {
                 id: id,
                 acreage: acreage
             },
             success: function success(data) {
                 if (data === 'success') {
                     $('.status-msg').text('Acreage has successfully been Updated!').css('display', 'block');
                     setTimeout(function () {
                         $('.status-msg').css('display', 'none');
                     }, 2500);
                 }
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function updateWellData() {
         let wellNamesString = '';

         $.each($('#well_name_select')[0].selectedOptions, function(key,value) {
             wellNamesString += value.value + '|';
         });
         wellNamesString = wellNamesString.slice(0, -1);

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
             url: '/lease-page/updateWellNames',
             data: {
                 permitId: $('#permit_id').val(),
                 wellNames: wellNamesString
             },
             success: function success(data) {

                 if (data === $('#permit_id').val()) {
                     location.reload();
                 }
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function updateLeaseNames() {
         let leaseNamesString = '';

         $.each($('#lease_name_select')[0].selectedOptions, function(key,value) {
             leaseNamesString += value.value + '|';
         });
         leaseNamesString = leaseNamesString.slice(0, -1);

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
             url: '/lease-page/updateLeaseNames',
             data: {
                 permitId: $('#permit_id').val(),
                 leaseNames: leaseNamesString
             },
             success: function success(data) {
                 if (data === $('#permit_id').val()) {
                     location.reload();
                 }

             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function updateAssignee(ownerId, assignee, interestArea) {
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
             url: '/lease-page/updateAssignee',
             data: {
                 ownerId: ownerId,
                 assigneeId: assignee,
                 interestArea: interestArea
             },
             success: function success(data) {

                 if (data === 'success') {
                     if (assignee !== "0") {
                         $("#owner_follow_up_" + ownerId).datepicker("setDate", "2");
                     }
                     $('.status-msg').text('Assignee has successfully been Updated!').css('display', 'block');
                     setTimeout(function () {
                         $('.status-msg').css('display', 'none');
                     }, 2500);
                 }
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function updateWellbore(ownerId, wellType, interestArea) {
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
             url: '/lease-page/updateWellType',
             data: {
                 ownerId: ownerId,
                 wellType: wellType,
                 interestArea: interestArea
             },
             success: function success(data) {
                 if (wellType !== "0") {
                     $("#owner_follow_up_" + ownerId).datepicker("setDate", "2");
                 }

                 $('.status-msg').text('Wellbore has successfully been Updated!').css('display', 'block');
                 setTimeout(function () {
                     $('.status-msg').css('display', 'none');
                 }, 2500);
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function getOwnerNotes( ownerId ) {
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
             url: '/lease-page/getNotes',
             data: {
                 ownerId: ownerId,
                 interestArea: $('#interest_area').val(),
                 leaseNames: $('#lease_string').val()
             },
             success: function success(data) {
                 if (data !== undefined && data !== '') {
                     let updatedNotes = '';

                     $.each(data, function (key, value) {
                         updatedNotes += '<span>'+value.notes+'</span>';
                     });
                     updatedNotes = $('<span>' + updatedNotes + '</span>');

                     $('#previous_owner_notes_'+ownerId).empty().append(updatedNotes.html());
                 } else {
                     $('#previous_owner_notes_'+ownerId).empty();
                 }
                 },
             error: function error(data) {
                 console.log(data);
                 $('.owner_notes').val('Note Submission Error. Make sure You Selected an Owner').text('Note Submission Error. Make sure You Selected an Owner');
             }
         });
     }

     function updateFollowUp(id, date, interestArea) {
         $.ajaxSetup({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });
         $.ajax({
             beforeSend: function beforeSend(xhr) {
                 xhr.setRequestHeader('X-CSRF-TOKEN', $("#token").attr('content'));},
             type: "PUT",
             url: '/lease-page/updateFollowUp',
             data: {
                 id: id,
                 date: date,
                 interestArea: interestArea
             },
             success: function success(data) {
                 console.log(data);
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function openOwnerPanel(id, tr, row) {

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
             url: '/lease-page/getOwnerInfo',
             data: {
                 id: id,
                 interestArea: $('#interest_area').val()
             },
             success: function success(data) {
                 let ownerBody = '';
                 if (($('#interest_area').val() === 'eagleford' || $('#interest_area').val() === 'wtx' || $('#interest_area').val() === 'tx') && $('#is_producing').val() === 'producing') {
                     ownerBody = '<div class="row">' +
                         '<div class="col-md-6">' +
                         '<h3 style="text-align: center;">Lease Info</h3>' +
                         '<div class="containers">' +
                         '<label for="lease_name_display_' + id + '">Lease Name: </label>' +
                         '<span id="lease_name_display_' + id + '"></span><br>' +
                         '<label for="lease_description_' + id + '">Lease Description: </label>' +
                         '<span id="lease_description_' + id + '"></span><br><br>' +
                         '<label for="rrc_lease_number_' + id + '">RRC Lease Number: </label>' +
                         '<span id="rrc_lease_number_' + id + '"></span><br>' +
                         '</div></div>' +
                         '<div class="col-md-6">' +
                         '<h3 style="text-align: center;">Well Production</h3>' +
                         '<div class="containers">' +
                         '<label class="addit_labels" for="active_well_count_' + id + '">Well Count: </label>' +
                         '<span id="active_well_count_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="first_prod_' + id + '">First Prod Date: </label>' +
                         '<span id="first_prod_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="last_prod_' + id + '">Last Prod Date: </label>' +
                         '<span id="last_prod_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="years_of_prod_' + id + '">Years of Production: </label>' +
                         '<span id="years_of_prod_' + id + '"></span><br>' +


                         '<label class="addit_labels" for="cum_prod_oil_' + id + '">Total Oil Production: </label>' +
                         '<span id="cum_prod_oil_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="cum_prod_gas_' + id + '">Total Gas Production: </label>' +
                         '<span id="cum_prod_gas_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="bbls_' + id + '">BBLS (OIL): </label>' +
                         '<span id="bbls_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="gbbls_' + id + '">MNX (GAS): </label>' +
                         '<span id="gbbls_' + id + '"></span><br>' +
                         '' +


                         '</div>' +
                         '</div></div>' +
                         '<div class="row"><div class="col-md-6">' +
                         '<h3 style="text-align: center;">Mineral Interest & Pricing Info.  </h3>' +
                         '<div class="containers">' +
                         '<div class="row">' +
                         '<div class="offset-2 col-md-5">' +
                         '<label class="addit_labels" for="decimal_interest_' + id + '">Decimal Interest: </label>' +
                         '<span id="decimal_interest_' + id + '"></span>' +
                         '</div>' +
                         '<div class="col-md-4">' +
                         '<label class="addit_labels" style="margin-left:-15%;" for="interest_type_' + id + '">Interest Type: </label>' +
                         '<span id="interest_type_' + id + '"></span>' +
                         '</div></div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="interest_type_' + id + '">Monthly Revenue: </label>' +
                         '<input type="text" style="margin-left:8.5%;" class="form-control monthly_revenue" id="monthly_revenue_' + id + '" disabled />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="owner_price_' + id + '">Pricing per NRA: </label>' +
                         '<input type="text" style="margin-left:10%;" class="form-control owner_price" name="owner_price" id="owner_price_' + id + '" placeholder="$" />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="net_royalty_acres_' + id + '">Net Royalty Acres: </label>' +
                         '<input type="text" style="margin-left:7.5%;" class="form-control net_royalty_acres" disabled id="net_royalty_acres_' + id + '" />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="total_price_for_interest_' + id + '">Total Price For Interest: </label>' +
                         '<input type="text" style="margin-left:2%;" class="form-control total_price_for_interest" disabled id="total_price_for_interest_' + id + '" />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="oil_price">Oil Price: </label>' +
                         '<input type="text" style="margin-left:18.5%;" class="form-control oil_price" disabled />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="gas_price">Gas Price: </label>' +
                         '<input type="text" style="margin-left:17%;" class="form-control gas_price" disabled />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="change_product">Change Product: </label>' +
                         '<select style="margin-left:9%;"  class="form-control change_product" id="change_product_' + id + '">' +
                         '<option value="none">Select a Product</option>' +
                         '<option value="oil" selected>Oil</option>' +
                         '<option value="gas" >Gas</option>' +
                         '</select>' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" id="bnp_label_' + id + '" for="bnp_' + id + '">BBL: </label>' +
                         '<input type="text" style="margin-left:22.8%;" class="form-control bnp" disabled id="bnp_' + id + '" />' +
                         '</div>' +
                         '<div class="form-group form-inline">' +
                         '<label class="addit_labels" for="ytp">Years to PayOff: </label>' +
                         '<input type="text" style="margin-left:10%;" class="form-control ytp" id="ytp_' + id + '" disabled />' +
                         '</div>' +
                         '</div></div>' +
                         '<div class="col-md-6">' +
                         '<div style="text-align:center;" class="col-md-12">' +
                         '<label style="font-size:20px; font-weight:bold;" for="notes">Owner Notes</label>' +
                         '<div class="previous_owner_notes" id="previous_owner_notes_' + id + '" name="previous_owner_notes" contenteditable="false"></div>' +
                         '</div>' +
                         '<div style="text-align:center;" class="col-md-12">' +
                         '<label style="font-size:20px; font-weight:bold;" for="owner_notes_' + id + '">Enter Owner Notes</label>' +
                         '<textarea rows="4" class="owner_notes" id="owner_notes_' + id + '" name="notes" style="width:100%;" placeholder="Enter Notes: "></textarea>' +
                         '<div class="col-md-12">' +
                         '<button type="button" id="update_owner_notes_btn_' + id + '" class="btn btn-primary update_owner_notes_btn">Update Notes</button>' +
                         '</div></div></div>';
                 } else if ($('#is_producing').val() === 'non-producing') {
                     let pricingBody = formatPricingFields(id, data.user_interest_type);

                      ownerBody = '<div class="row">' +
                         '<div class="col-md-6">' +
                         '<h3 style="text-align: center;">Lease Info</h3>' +
                         '<div class="containers">' +
                         '<label for="lease_name_display_' + id + '">Lease Name: </label>' +
                         '<span id="lease_name_display_' + id + '"></span><br>' +
                         '</div></div>' +
                         '<div class="col-md-6">' +
                         '<h3 style="text-align: center;">Well Production</h3>' +
                         '<div class="containers">' +
                         '<label class="addit_labels" for="active_well_count_' + id + '">Well Count: </label>' +
                         '<span id="active_well_count_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="first_prod_' + id + '">First Prod Date: </label>' +
                         '<span id="first_prod_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="last_prod_' + id + '">Last Prod Date: </label>' +
                         '<span id="last_prod_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="years_of_prod_' + id + '">Years of Production: </label>' +
                         '<span id="years_of_prod_' + id + '"></span><br>' +


                         '<label class="addit_labels" for="cum_prod_oil_' + id + '">Total Oil Production: </label>' +
                         '<span id="cum_prod_oil_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="cum_prod_gas_' + id + '">Total Gas Production: </label>' +
                         '<span id="cum_prod_gas_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="bbls_' + id + '">BBLS (OIL): </label>' +
                         '<span id="bbls_' + id + '"></span><br>' +
                         '<label class="addit_labels" for="gbbls_' + id + '">MNX (GAS): </label>' +
                         '<span id="gbbls_' + id + '"></span><br>' +
                         '' +


                         '</div>' +
                         '</div></div>' +
                         '<div class="row"><div class="col-md-6">' +
                         '<h3 style="text-align: center;">Mineral Interest & Pricing Info.  </h3>' +
                         '<div class="containers">' +
                         '<div class="row">' +
                         '<div class="offset-2 col-md-5">' +

                          pricingBody +

                         '<div class="col-md-6">' +
                         '<div style="text-align:center;" class="col-md-12">' +
                         '<label style="font-size:20px; font-weight:bold;" for="notes">Owner Notes</label>' +
                         '<div class="previous_owner_notes" id="previous_owner_notes_' + id + '" name="previous_owner_notes" contenteditable="false"></div>' +
                         '</div>' +
                         '<div style="text-align:center;" class="col-md-12">' +
                         '<label style="font-size:20px; font-weight:bold;" for="owner_notes_' + id + '">Enter Owner Notes</label>' +
                         '<textarea rows="4" class="owner_notes" id="owner_notes_' + id + '" name="notes" style="width:100%;" placeholder="Enter Notes: "></textarea>' +
                         '<div class="col-md-12">' +
                         '<button type="button" id="update_owner_notes_btn_' + id + '" class="btn btn-primary update_owner_notes_btn">Update Notes</button>' +
                         '</div></div></div>';
                 }


                 if ( row.child.isShown() ) {
                     row.child.hide();
                     tr.removeClass('shown');
                 } else {
                     row.child(ownerBody).show();
                     tr.addClass('shown');
                 }

                 let price = 0.0;
                 if (data.price !== null) {
                     price = data.price;
                 }

                 $('#lease_name_display_'+id).text(' ' + data.lease_name);
                 $('#lease_description_'+id).text(' ' + data.lease_description);
                 $('#rrc_lease_number_'+id).text(' ' + data.rrc_lease_number);

                 $('#owner_price_'+id).val(' $' + price);

                 $('#decimal_interest_'+id).text(' ' + data.owner_decimal_interest);
                 $('#interest_type_'+id).text(' ' + data.owner_interest_type);

                 let monthlyRevenue = data.tax_value / 12;
                 monthlyRevenue = monthlyRevenue.toFixed(2);
                 monthlyRevenue = numberWithCommas(monthlyRevenue);
                 $('#monthly_revenue_' +id).val(monthlyRevenue);

                 $('#active_well_count_' + id).text(' ' + $('#well_count').val());
                 $('#first_prod_'+id).text(' ' + $('#first_month').text());
                 $('#last_prod_'+id).text(' ' + $('#last_month').text());
                 $('#cum_prod_oil_'+id).text(' ' + $('#total_oil').text());
                 $('#cum_prod_gas_'+id).text(' ' + $('#total_gas').text());
                 $('#years_of_prod_'+id).text(' ' + $('#years_of_prod').text());
                 $('#bbls_'+id).text(' ' + $('#bbls').text());
                 $('#gbbls_'+id).text(' ' + $('#gbbls').text());

                 let ownerPrice = $('#owner_price_'+id).val();
                 if (ownerPrice !== undefined) {
                     ownerPrice = ownerPrice.replace('$', '');
                 } else {
                     ownerPrice = 0;
                 }

                 let netRoyaltyAcres = data.owner_decimal_interest / .125 * $('.acreage').val();
                 netRoyaltyAcres = netRoyaltyAcres.toFixed(4);
                 $('#net_royalty_acres_'+id).val(netRoyaltyAcres);

                 let total = ownerPrice * $('#net_royalty_acres_'+id).val();
                 let totalPriceForInterest = total.toFixed(2);
                 let totalPriceForInterestWithCommas = numberWithCommas(totalPriceForInterest);

                 $('#total_price_for_interest_'+id).val( '$' + totalPriceForInterestWithCommas);

                 let neededIncome = totalPriceForInterest / data.owner_decimal_interest;
                 let bnp = neededIncome / data.oilPrice;
                 bnp = bnp.toFixed(2);
                 let bnpWithComma = numberWithCommas(bnp);

                 $('.oil_price').val(data.oilPrice);
                 $('.gas_price').val(data.gasPrice);
                 $('#bnp_' + id).val(bnpWithComma);

                 let bbls = $('#bbls').text();
                 bbls = bbls.replace(',', '');

                 let ytp = bnp / bbls;

                 ytp = ytp.toFixed(2);
                 let ytpWithComma = numberWithCommas(ytp);
                 $('#ytp_' + id).val(ytpWithComma);

                 getOwnerNotes( id );
             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function formatPricingFields(id, interestType) {
         let body = '';
         if (interestType === 'RI') {
             body = '<label class="addit_labels" for="decimal_interest_' + id + '">Decimal Interest: </label>' +
                 '<input type="text" class="form-control" id="decimal_interest_' + id + '" />' +
                 '</div>' +
                 '<div class="col-md-4">' +
                 '<label class="addit_labels" style="margin-left:-15%;" for="interest_type_' + id + '">Interest Type: </label>' +
                 '<select class="interest_type form-control" id="interest_type_' + id + '"><option value="RI">Royalty Interest</option><option value="NMA">Net Mineral Acres</option></select>' +
                 '</div></div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="interest_type_' + id + '">Monthly Revenue: </label>' +
                 '<input type="text" style="margin-left:8.5%;" class="form-control monthly_revenue" id="monthly_revenue_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="owner_price_' + id + '">Pricing per NRA: </label>' +
                 '<input type="text" style="margin-left:10%;" class="form-control owner_price" name="owner_price" id="owner_price_' + id + '" placeholder="$" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="net_royalty_acres_' + id + '">Net Royalty Acres: </label>' +
                 '<input type="text" style="margin-left:7.5%;" class="form-control net_royalty_acres" disabled id="net_royalty_acres_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="total_price_for_interest_' + id + '">Total Price For Interest: </label>' +
                 '<input type="text" style="margin-left:2%;" class="form-control total_price_for_interest" disabled id="total_price_for_interest_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="oil_price">Oil Price: </label>' +
                 '<input type="text" style="margin-left:18.5%;" class="form-control oil_price" disabled />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="gas_price">Gas Price: </label>' +
                 '<input type="text" style="margin-left:17%;" class="form-control gas_price" disabled />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="change_product">Change Product: </label>' +
                 '<select style="margin-left:9%;"  class="form-control change_product" id="change_product_' + id + '">' +
                 '<option value="none">Select a Product</option>' +
                 '<option value="oil" selected>Oil</option>' +
                 '<option value="gas" >Gas</option>' +
                 '</select>' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" id="bnp_label_' + id + '" for="bnp_' + id + '">BBL: </label>' +
                 '<input type="text" style="margin-left:22.8%;" class="form-control bnp" disabled id="bnp_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="ytp">Years to PayOff: </label>' +
                 '<input type="text" style="margin-left:10%;" class="form-control ytp" id="ytp_' + id + '" disabled />' +
                 '</div>' +
                 '</div></div>';
         } else if (interestType === 'NRA') {
             body = '<label class="addit_labels" for="decimal_interest_' + id + '">Leased @: </label>' +
                 '<input type="text" class="form-control" id="decimal_interest_' + id + '" />' +
                 '</div>' +
                 '<div class="col-md-4">' +
                 '<label class="addit_labels" style="margin-left:-15%;" for="interest_type_' + id + '">Interest Type: </label>' +
                 '<select class="interest_type form-control" id="interest_type_' + id + '"><option value="RI">Royalty Interest</option><option value="NMA">Net Mineral Acres</option></select>' +
                 '</div></div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="interest_type_' + id + '">Monthly Revenue: </label>' +
                 '<input type="text" style="margin-left:8.5%;" class="form-control monthly_revenue" id="monthly_revenue_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="owner_price_' + id + '">Pricing per NMA: </label>' +
                 '<input type="text" style="margin-left:10%;" class="form-control owner_price" name="owner_price" id="owner_price_' + id + '" placeholder="$" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="net_mineral_acres_' + id + '">Net Mineral Acres: </label>' +
                 '<input type="text" style="margin-left:7.5%;" class="form-control net_mineral_acres" disabled id="net_mineral_acres_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="total_price_for_interest_' + id + '">Total Price For Interest: </label>' +
                 '<input type="text" style="margin-left:2%;" class="form-control total_price_for_interest" disabled id="total_price_for_interest_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="oil_price">Oil Price: </label>' +
                 '<input type="text" style="margin-left:18.5%;" class="form-control oil_price" disabled />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="gas_price">Gas Price: </label>' +
                 '<input type="text" style="margin-left:17%;" class="form-control gas_price" disabled />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="change_product">Change Product: </label>' +
                 '<select style="margin-left:9%;"  class="form-control change_product" id="change_product_' + id + '">' +
                 '<option value="none">Select a Product</option>' +
                 '<option value="oil" selected>Oil</option>' +
                 '<option value="gas" >Gas</option>' +
                 '</select>' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" id="bnp_label_' + id + '" for="bnp_' + id + '">BNP: </label>' +
                 '<input type="text" style="margin-left:22.8%;" class="form-control bnp" disabled id="bnp_' + id + '" />' +
                 '</div>' +
                 '<div class="form-group form-inline">' +
                 '<label class="addit_labels" for="ytp">WNP: </label>' +
                 '<input type="text" style="margin-left:10%;" class="form-control wnp" id="wnp_' + id + '" disabled />' +
                 '</div>' +
                 '</div></div>';
         }

         return body;
     }

     function updatePrice(id) {
         let ownerPrice = $('#owner_price_' +id).val();
         ownerPrice = ownerPrice.replace('$', '');

         let total = ownerPrice * $('#net_royalty_acres_' +id).val();
         let totalPriceForInterest = total.toFixed(2);
         let totalPriceForInterestWithComma = numberWithCommas(totalPriceForInterest);

         $('#total_price_for_interest_' +id).val( '$' + totalPriceForInterestWithComma);

         let neededIncome = totalPriceForInterest / $('#decimal_interest_'+id).text();
         let bnp = neededIncome / $('.oil_price').val();
         bnp = bnp.toFixed(2);
         let bnpWithComma = numberWithCommas(bnp);

         $('#bnp_' + id).val(bnpWithComma);

         let bbls = $('#bbls').text();
         bbls = bbls.replace(',', '');

         let ytp = bnp / bbls;

         ytp = ytp.toFixed(2);
         let ytpWithComma = numberWithCommas(ytp);
         $('#ytp_' + id).val(ytpWithComma);

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
             url: '/lease-page/update/OwnerPrice',
             data: {
                 id: id,
                 price: ownerPrice,
                 interestArea: $('#interest_area').val()
             },
             success: function success(data) {
                 console.log(data);

             },
             error: function error(data) {
                 console.log(data);
             }
         });
     }

     function changeProduct(id, product) {
         let productPrice = '';

         if (product === 'gas') {
             productPrice = $('.gas_price').val();
             $('#bnp_label_' + id).text('MCF: ');
         } else if (product === 'oil') {
             productPrice = $('.oil_price').val();
             $('#bnp_label_' + id).text('BBL: ');

         }

         let ownerPrice = $('#owner_price_' +id).val();
         ownerPrice = ownerPrice.replace('$', '');

         let total = ownerPrice * $('#net_royalty_acres_' +id).val();
         let totalPriceForInterest = total.toFixed(2);
         let totalPriceForInterestWithComma = numberWithCommas(totalPriceForInterest);

         $('#total_price_for_interest_' +id).val( '$' + totalPriceForInterestWithComma);

         let neededIncome = totalPriceForInterest / $('#decimal_interest_'+id).text();
         let bnp = neededIncome / productPrice;
         bnp = bnp.toFixed(2);
         let bnpWithComma = numberWithCommas(bnp);

         $('#bnp_' + id).val(bnpWithComma);

         let bbls = $('#bbls').text();
         bbls = bbls.replace(',', '');

         let ytp = bnp / bbls;

         ytp = ytp.toFixed(2);
         let ytpWithComma = numberWithCommas(ytp);
         $('#ytp_' + id).val(ytpWithComma);

     }

     function updateNotes(id, leaseName, interestArea) {
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
             url: '/lease-page/updateNotes',
             data: {
                 id: id,
                 leaseName: leaseName,
                 notes: $('#owner_notes_' +id).val(),
                 interestArea: interestArea
             },
             success: function success(data) {
                 $("#owner_follow_up_" + id).datepicker("setDate", "2");
                 let updatedNotes = '';

                 $.each(data, function (key, value) {
                     updatedNotes += '<span>'+value.notes+'</span>';
                 });
                 updatedNotes = $('<span>' + updatedNotes + '</span>');

                 $('#previous_owner_notes_'+ id).empty().append(updatedNotes.html());
                 $('#owner_notes_'+id).val('').text('');

                 $('#assignee_' + id).val($('#user_id').val());
             },
             error: function error(data) {
                 console.log(data);
                 $('#owner_notes_'+id).val('Note Submission Error. Make sure You Selected an Owner').text('Note Submission Error. Make sure You Selected an Owner');
             }
         });
     }

     function deleteNote(id, noteId) {
         let response = confirm('Are you sure you want to delete this note?');

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
                 url: '/lease-page/delete-note',
                 data: {
                     id: noteId
                 },
                 success: function success(data) {

                     let updatedNotes = '';

                     $.each(data, function (key, value) {
                         updatedNotes += '<span>'+value.notes+'</span>';
                     });
                     updatedNotes = $('<span>' + updatedNotes + '</span>');

                     $('#previous_owner_notes_'+ id).empty().append(updatedNotes.html());
                 },
                 error: function error(data) {
                     console.log(data);
                 }
             });
         }
     }

     function openPhoneModal(id, interestArea) {
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
                 interestArea: interestArea
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

     function numberWithCommas(x) {
         return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
     }
 });








