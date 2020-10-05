$(document).ready(function () {
    let adminPermitTable = '';

    //EAGLE PERMIT TABLE
    $('#admin_permit_table').on('click', 'td.mmp-details-control', function () {
        let id = $(this)[0].id;
        let splitId = id.split('_');
        let permitId = splitId[1];
        let reportedOperator = $('#reported_operator_' + permitId).val();
        let tr = $(this).closest('tr');
        let row = adminPermitTable.row(tr);

        moreAdminData(id, tr, permitId, reportedOperator, row);

    });

    $('#update_permit_btn').on('click', function() {
        let county = $('#county_select').val();

        if (county === '') {
            alert('Please select a county');
        } else {
            $('.loader').css('display', 'inline-block');

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
                url: '/admin/updatePermits',
                data: {
                    county: county
                },
                success: function success(data) {
                    $('.loader').css('display', 'none');
                    console.log(data);

                    if (data !== '') {
                        let rows = '';

                        $.each (data, function( key, value) {
                            rows += '<tr>' +
                                '<td id="id_'+ value.permit_id+'" class="text-center mmp-details-control"><i style="cursor:pointer;" class="far fa-dot-circle"></i></td>' +
                                '<td class="text-center">'+ value.county_parish +'</td>' +
                                '<td class="text-center">'+ value.reported_operator +'</td>' +
                                '<td class="text-center">'+ value.lease_name +'</td>' +
                                '</tr>'
                        });

                        console.log(rows);

                        adminPermitTable = $('#admin_permit_table').append(rows).DataTable({
                            paging: true,
                            destroy: true,
                        });
                    } else {
                        let messages = $('.alert-danger');
                        let successHtml = '<div class="alert alert-danger">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<strong><i class="glyphicon glyphicon-ok-sign push-5-r"></</strong> There was a problem Updating Database' +
                            '</div>';
                        $(messages).html(successHtml);
                    }
                },
                error: function error(data) {
                    console.log(data);
                }
            });
        }
    });











    function moreAdminData(id, tr, permitId, reportedOperator, row) {
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
                reportedOperator: reportedOperator
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
                    '<div class="row"><div class="col-md-4">' +
                    '<div class="map" id="map_'+ permitId+'"></div></div>' +
                    '<div class="col-md-6"><div style="margin-top:1.5%;">' +
                    '</div></div></div>';

                if ( row.child.isShown() ) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    row.child( permitBody ).show();
                    tr.addClass('shown');

                    try {
                        let geoPoints = data['permit'].btm_geometry.replace(/\s/g, '').replace(/},/g, '},dd').replace('(', '').replace(')', '').split(',dd');
                        let obj = [];
                        geoPoints.push(data.leaseGeo);
                        let map;
                        let bounds;


                        for (let j in geoPoints) {
                            if (j == 0) {
                                map = new google.maps.Map(document.getElementById('map_'+ permitId), {
                                    center: JSON.parse(geoPoints[j]),
                                    zoom: 13,
                                    mapTypeId: google.maps.MapTypeId.HYBRID
                                });

                            }
                            obj.push(JSON.parse(geoPoints[j]));
                        }

                        let locationInfowindow = new google.maps.InfoWindow({
                            content: 'What info do we want in here.',
                        });

                        let marker = new google.maps.Marker({
                            position: JSON.parse(geoPoints[0]),
                            map: map,
                            infowindow: locationInfowindow
                        });

                        google.maps.event.addListener(marker, 'click', function() {
                            this.infowindow.open(map, this);
                        });

                        function ResizeMap() {
                            google.maps.event.trigger(map, "resize");
                        }

                        $("#VehicleMovementModal").on('shown', function () {
                            ResizeMap();
                        });

                        bounds = new google.maps.LatLngBounds();
                        google.maps.event.addListenerOnce(map, 'tilesloaded', function (evt) {

                            bounds = map.getBounds();
                        });

                        let input = /** @type {!HTMLInputElement} */(
                            document.getElementById('pac-input'));
                        let types = document.getElementById('type-selector');
                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
                        map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

                        let polygon = new google.maps.Polygon({
                            path: obj,
                            geodesic: true,
                            strokeColor: '#091096',
                            strokeOpacity: 1.0,
                            strokeWeight: 2,
                            fillColor: '#B1AAA9',
                            fillOpacity: 0.35,
                        });
                        polygon.setMap(map);
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





});