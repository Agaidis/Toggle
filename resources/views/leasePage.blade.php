@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Lease Information & Mineral Owners</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'regular')
                                    <div class="button_panel">
                                        <a href="{{ url('wellbore') }}">
                                            <button type="button" class="btn btn-primary dashboard_btns"
                                                    id="welbore_btn">Wellbore
                                            </button>
                                        </a>
                                        <a href="{{ url('user-mmp') }}">
                                            <button type="button" style="margin-left:5%;"
                                                    class="btn btn-primary dashboard_btns"
                                                    id="user_mmp_btn">{{Auth::user()->name}}</button>
                                        </a>
                                        <button type="button" style="margin-left:5%;"
                                                class="btn btn-primary dashboard_btns" data-target="#modal_open_wells"
                                                data-toggle="modal" id="well_count_btn">Well Count: {{$count}}</button>

                                    </div>
                            </div>
                            <input type="hidden" id="lease_string" value="{{$leaseString}}" />
                            <div class="col-md-5">
                                <label style="margin-top:20px;" class="labels">Well Name(s)</label>:
                                <select id="well_name_select" class="form-control" multiple="multiple">
                                    @foreach ($selectWells as $selectWell)
                                        @if ($selectWell != '')
                                            @if (in_array($selectWell->LeaseName, $wellArray)  )
                                                <option selected
                                                        value="{{$selectWell->LeaseName}}">{{$selectWell->LeaseName}}</option>
                                            @else
                                                <option value="{{$selectWell->LeaseName}}">{{$selectWell->LeaseName}}</option>
                                            @endif
                                        @endif
                                    @endforeach
                                </select>
                                <button type="button" id="refresh_well_data_btn" class="btn btn-success">Refresh Well
                                    Data
                                </button>
                                <br>
                            </div>
                        </div>
                        <input type="hidden" id="user_id" value="{{Auth::user()->id}}"/>
                        <input type="hidden" id="well_count" value="{{$count}}"/>
                        <input type="hidden" id="permit_id" value="{{$permitValues->id}}"/>
                        <input type="hidden" id="hidden_permit_notes" value="{{$notes}}"/>
                        <input type="hidden" id="lease_name" name="lease_name" value="{{$leaseName}}"/>
                        <input type="hidden" id="interest_area" name="interest_area" value="{{$interestArea}}"/>
                        <input type="hidden" id="is_producing" name="is_producing" value="{{$isProducing}}"/>


                        <div style="margin-top:1.5%;" class="col-md-12">
                            <div class="row">
                                <div style="text-align:center;" class="col-md-4">
                                    <span style="font-size:20px;">
                                    <label class="labels">Operator</label>: {{$permitValues->reported_operator}}
                                </span>
                                    <div class="col-md-12" id="acreage_container">
                                        <label class="labels">Acreage : </label>
                                        <input type="text" placeholder="Acreage" class="acreage"
                                               id="acreage_{{$permitValues->id}}" name="acreage"
                                               value="{{$permitValues->acreage}}">
                                    </div>
                                    <label style="font-size:20px; font-weight:bold;" for="notes">Lease Notes</label>
                                    <div class="previous_notes" id="previous_notes" name="previous_notes"
                                         contenteditable="false"></div>

                                </div>
                                <div class="col-md-4">
                                    <div class="map" id="proMap"></div>
                                </div>
                                <div class="col-md-4">
                                    <label for="county">County: </label>
                                    <span id="county">{{$permitValues->county_parish}}</span><br>

                                    @if ($permitValues->range != '')
                                        <label for="Township">Township: </label>
                                        <span id="Township">{{$permitValues->township}}</span><br>
                                    @endif
                                    @if ($permitValues->range != '')
                                        <label for="Range">Range: </label>
                                        <span id="Range">{{$permitValues->range}}</span><br>
                                    @endif

                                    @if ($permitValues->section != '')
                                        <label for="Section">Section: </label>
                                        <span id="Section">{{$permitValues->section}}</span><br>
                                    @endif

                                    @if ($permitValues->drill_type != '')
                                        <label for="DrillType">Drill Type: </label>
                                        <span id="DrillType">{{$permitValues->drill_type}}</span><br>
                                    @endif

                                    @if ($permitValues->permit_type != '')
                                        <label for="PermitType">Permit Type: </label>
                                        <span id="PermitType">{{$permitValues->permit_type}}</span><br>
                                    @endif

                                    @if ($permitValues->well_type != '')
                                        <label for="WellType">Well Type: </label>
                                        <span id="WellType">{{$permitValues->well_type}}</span><br>
                                    @endif

                                    @if ($permitValues->block != '')
                                        <label for="Block">Block: </label>
                                        <span id="Block">{{$permitValues->block}}</span><br>
                                    @endif

                                    <label for="total_gas">Total Gas Production: </label>
                                    <span id="total_gas">{{$totalGasWithComma}}</span><br>

                                    <label for="total_oil">Total Oil Production: </label>
                                    <span id="total_oil">{{$totalOilWithComma}}</span><br>

                                    <label for="bbls">BBLS (OIL): </label>
                                    <span id="bbls">{{$bblsWithComma}}</span><br>

                                    <label for="gbbls">MNX (GAS): </label>
                                    <span id="gbbls">{{$gbblsWithComma}}</span><br>

                                    @if ($permitValues->approved_date != '')
                                        <?php $approvedDate = explode('T', $permitValues->approved_date) ?>
                                        <label for="approved_date">Approved Date: </label>
                                        <span id="ApprovedDate">{{$approvedDate[0]}}</span><br>
                                    @endif

                                    @if ($permitValues->submitted_date != '')
                                        <?php $submittedDate = explode('T', $permitValues->submitted_date) ?>
                                        <label for="submitted_date">Submitted Date: </label>
                                        <span id="SubmittedDate">{{$submittedDate[0]}}</span><br>
                                    @endif

                                    @if ($permitValues->expiration_primary_term != '')
                                        <label for="expiration_primary_term">Expiration Primary Term: </label>
                                        <span id="expiration_primary_term">{{$permitValues->expiration_primary_term}}</span>
                                        <br>
                                    @endif

                                    @if ($permitValues->survey != '')
                                        <label for="Survey">Survey: </label>
                                        <span id="Survey">{{$permitValues->survey}}</span><br>
                                    @endif


                                    @if ($permitValues->abstract != '')
                                        <label for="Abstract">Abstract: </label>
                                        <span id="Abstract">{{$permitValues->abstract}}</span><br>
                                    @endif

                                    @if ($permitValues->district != '')
                                        <label for="District">District: </label>
                                        <span id="District">{{$permitValues->district}}</span><br>
                                    @endif

                                    <?php $oldestDate = explode('T', $oldestDate) ?>
                                    <label for="first_month">First month in Production: </label>
                                    <span id="first_month">{{$oldestDate[0]}}</span><br>

                                    <?php $latestDate = explode('T', $latestDate) ?>
                                    <label for="last_month">Last month in Production: </label>
                                    <span id="last_month">{{$latestDate[0]}}</span><br>

                                    <label for="last_month">Years of Production: </label>
                                    <span id="years_of_prod">{{$yearsOfProduction}}</span>

                                </div>

                            </div>
                            <div class="alert-success status-msg"></div>
                            <hr style="margin-top: 2%!important; margin-bottom:1%;"/>
                        </div>
                        @endif

                        @if (in_array($interestArea, $txInterestAreas ))
                        <div class="row">
                            <div class="col-md-5">
                                <label class="labels">Upload Owners</label>:
                                <input type="file" class="form-control" id="file_upload"/>
                                <button type="button" id="upload_file_btn" class="btn btn-success">Submit Upload File</button>
                            </div>
                            <div class="col-md-5">
                                <label class="labels">Mineral Owners</label>:
                                <select id="lease_name_select" class="form-control" multiple="multiple">
                                    @foreach ($mineralOwnerLeases as $lease)
                                        @if (in_array($lease->lease_name, $leaseArray) )
                                            <option selected
                                                    value="{{$lease->lease_name}}">{{$lease->lease_name}}</option>
                                        @else
                                            <option value="{{$lease->lease_name}}">{{$lease->lease_name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                                <button type="button" id="refresh_lease_data_btn" class="btn btn-success">Refresh
                                    Owners
                                </button>
                                <br>
                            </div>
                        </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="overflow-x:auto;">
                                        <table class="table table-hover table-responsive-md table-bordered owner_table"
                                               style="width:1475px;">
                                            <thead>
                                            <tr>
                                                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'regular' )
                                                    <th class="text-center">More Data</th>
                                                    <th class="text-center">Assignee/Follow-Up</th>
                                                    <th class="text-center">Wellbore Type</th>
                                                    <th class="text-center" style="width:100px;">Contact</th>
                                                    <th class="text-center">Follow-Up</th>
                                                    <th class="text-center">Owner</th>
                                                    <th class="text-center">ODI</th>
                                                    <th class="text-center">% Type</th>
                                                @else
                                                    <th class="text-center">Col 1</th>
                                                    <th class="text-center">Col 2</th>
                                                    <th class="text-center">Col 3</th>
                                                    <th class="text-center" style="width:100px;">Contact</th>
                                                    <th class="text-center">Col 4</th>
                                                    <th class="text-center">Owner</th>
                                                    <th class="text-center">ODI</th>
                                                    <th class="text-center">Col 7</th>
                                                    <th class="text-center">Col 8</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($owners as $owner)
                                                <tr class="owner_row" id="owner_row_{{$owner->id}}">
                                                    @if (Auth::user()->role === 'admin'  || Auth::user()->role === 'regular')
                                                        <td id="id_{{$owner->id}}"
                                                            class="text-center owner-details-control view_owner"><i
                                                                    style="cursor:pointer;"
                                                                    class="far fa-dot-circle"></i></td>
                                                        <td class="text-center">
                                                            @if (Auth::user()->name === 'Billy Moreaux' && ($owner->assignee != null || $owner->assignee != 0))
                                                                <select class="form-control owner_assignee"
                                                                        id="assignee_{{$owner->id}}">
                                                                    @elseif (($owner->assignee != null && $owner->assignee != '0'))
                                                                        <select disabled
                                                                                class="form-control owner_assignee"
                                                                                id="assignee_{{$owner->id}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee"
                                                                                        id="assignee_{{$owner->id}}">@endif
                                                                                    <option selected value="0">Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($user->id == $owner->assignee)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                        </select>
                                                        </td>
                                                        <td class="text-center">
                                                            @if (Auth::user()->name === 'Billy Moreaux' && ($owner->wellbore_type != null || $owner->wellbore_type != 0))
                                                                <select class="form-control wellbore_dropdown"
                                                                        id="wellbore_dropdown_{{$owner->id}}">
                                                                    @elseif (($owner->wellbore_type != null && $owner->wellbore_type != '0'))
                                                                        <select disabled
                                                                                class="form-control wellbore_dropdown"
                                                                                id="wellbore_dropdown_{{$owner->id}}">
                                                                            @else
                                                                                <select class="form-control wellbore_dropdown"
                                                                                        id="wellbore_dropdown_{{$owner->id}}">
                                                                                    @endif
                                                                                    @if ($owner->wellbore_type == 1)
                                                                                        <option value="0">None</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore_type}}">
                                                                                            1
                                                                                        </option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore_type == 2)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore_type}}">
                                                                                            2
                                                                                        </option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore_type == 3)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore_type}}">
                                                                                            3
                                                                                        </option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore_type == 4)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore_type}}">
                                                                                            4
                                                                                        </option>
                                                                                    @else
                                                                                        <option selected value="0">
                                                                                            None
                                                                                        </option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @endif
                                                                                </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-primary open_phone_modal"
                                                                    id="open_phone_{{$owner->id}}"
                                                                    data-target="#modal_add_phone" data-toggle="modal">
                                                                Contact Info
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($owner->follow_up_date != '')
                                                                <i class="fas fa-calendar-alt"></i> <input
                                                                        class="form-control owner_follow_up"
                                                                        id="owner_follow_up_{{$owner->id}}"
                                                                        value="{{date('M j, Y', strtotime($owner->follow_up_date))}}"/>
                                                            @else
                                                                <i class="fas fa-calendar-alt"></i> <input
                                                                        class="form-control owner_follow_up"
                                                                        id="owner_follow_up_{{$owner->id}}"/>
                                                            @endif
                                                        </td>
                                                        <td class="text-center"><a
                                                                    href="{{url( 'owner/' . $interestArea . '/' . $owner->owner) . '/' . $isProducing}}">{{$owner->owner}}</a><br>{{$owner->owner_address}}
                                                            <br>{{$owner->owner_city}}, {{$owner->owner_zip}}</td>
                                                        <td class="text-center">{{$owner->owner_decimal_interest}}</td>
                                                        <td class="text-center">{{$owner->owner_interest_type}}</td>
                                                    @else
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-primary add_phone_btn"
                                                                    id="add_phone_{{$owner->id}}"
                                                                    data-target="#modal_add_phone" data-toggle="modal">
                                                                Contact Info
                                                            </button>
                                                        </td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"><a
                                                                    href="{{url( 'owner/' . $interestArea . '/' . $owner->owner . '/' . $isProducing)}}">{{$owner->owner}}</a><br>{{$owner->owner_address}}
                                                            <br>{{$owner->owner_city}}, {{$owner->owner_zip}}</td>
                                                        <td class="text-center">{{$owner->owner_decimal_interest}}</td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <caption id="owner_table_caption">Owners</caption>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>


                        @elseif (in_array($interestArea, $nonTexasInterestAreas ))
                            <div class="row">
                                <div class="col-md-12">
                                    <div style="overflow-x:auto;">
                                        <table class="table table-hover table-responsive-md table-bordered owner_table"
                                               style="width:1475px;">
                                            <thead>

                                            <tr>
                                                @if (Auth::user()->role === 'admin' || Auth::user()->role === 'regular' )
                                                    <th class="text-center">More Data</th>
                                                    <th class="text-center">Assignee</th>
                                                    <th class="text-center">Wellbore Type</th>
                                                    <th class="text-center" style="width:100px;">Contact</th>
                                                    <th class="text-center">Follow-Up</th>
                                                    <th class="text-center">Grantor/Owner</th>
                                                    <th class="text-center">Grantee</th>
                                                    <th class="text-center">Record Date/Term/Extension</th>
                                                    <th class="text-center">Acres</th>
                                                @else
                                                    <th class="text-center">Col 1</th>
                                                    <th class="text-center">Col 2</th>
                                                    <th class="text-center">Col 3</th>
                                                    <th class="text-center" style="width:100px;">Contact</th>
                                                    <th class="text-center">Col 4</th>
                                                    <th class="text-center">Owner</th>
                                                    <th class="text-center">Grantee</th>
                                                    <th class="text-center">Record</th>
                                                    <th class="text-center">Acres</th>
                                                @endif
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach ($owners as $owner)
                                                <tr class="owner_row" id="owner_row_{{$owner->LeaseId}}">
                                                    @if (Auth::user()->role === 'admin'  || Auth::user()->role === 'regular')
                                                        <td id="id_{{$owner->LeaseId}}"
                                                            class="text-center owner-details-control view_owner"><i
                                                                    style="cursor:pointer;"
                                                                    class="far fa-dot-circle"></i></td>
                                                        <td class="text-center">
                                                            @if (Auth::user()->name === 'Billy Moreaux' && ($owner->assignee != null || $owner->assignee != 0))
                                                                <select class="form-control owner_assignee"
                                                                        id="assignee_{{$owner->LeaseId}}">
                                                                    @elseif (($owner->assignee != null && $owner->assignee != '0'))
                                                                        <select disabled
                                                                                class="form-control owner_assignee"
                                                                                id="assignee_{{$owner->LeaseId}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee"
                                                                                        id="assignee_{{$owner->LeaseId}}">
                                                                                    @endif
                                                                                    <option selected value="0">Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($user->id == $owner->assignee)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                        </select>

                                                        </td>
                                                        <td class="text-center">
                                                            @if (Auth::user()->name === 'Billy Moreaux' && ($owner->wellbore != null || $owner->wellbore != 0))
                                                                <select class="form-control wellbore_dropdown"
                                                                        id="wellbore_dropdown_{{$owner->LeaseId}}">
                                                                    @elseif (($owner->wellbore != null && $owner->wellbore != '0'))
                                                                        <select disabled
                                                                                class="form-control wellbore_dropdown"
                                                                                id="wellbore_dropdown_{{$owner->LeaseId}}">
                                                                            @else
                                                                                <select class="form-control wellbore_dropdown"
                                                                                        id="wellbore_dropdown_{{$owner->LeaseId}}">
                                                                                    @endif
                                                                                    @if ($owner->wellbore == 1)
                                                                                        <option value="0">None</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore}}">
                                                                                            1
                                                                                        </option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore == 2)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore}}">
                                                                                            2
                                                                                        </option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore == 3)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore}}">
                                                                                            3
                                                                                        </option>
                                                                                        <option value="4">4</option>
                                                                                    @elseif ($owner->wellbore == 4)
                                                                                        <option value="0">None</option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option selected
                                                                                                value="{{$owner->wellbore}}">
                                                                                            4
                                                                                        </option>
                                                                                    @else
                                                                                        <option selected value="0">
                                                                                            None
                                                                                        </option>
                                                                                        <option value="1">1</option>
                                                                                        <option value="2">2</option>
                                                                                        <option value="3">3</option>
                                                                                        <option value="4">4</option>
                                                                                    @endif
                                                                                </select>
                                                        </td>
                                                        <td class="text-center">
                                                            <button class="btn btn-primary open_phone_modal"
                                                                    id="open_phone_{{$owner->LeaseId}}"
                                                                    data-target="#modal_add_phone" data-toggle="modal">
                                                                Contact Info
                                                            </button>
                                                        </td>
                                                        <td class="text-center">
                                                            @if ($owner->follow_up_date != '')
                                                                <i class="fas fa-calendar-alt"></i> <input
                                                                        class="form-control owner_follow_up"
                                                                        id="owner_follow_up_{{$owner->LeaseId}}"
                                                                        value="{{date('M j, Y', strtotime($owner->follow_up_date))}}"/>
                                                            @else
                                                                <i class="fas fa-calendar-alt"></i> <input
                                                                        class="form-control owner_follow_up"
                                                                        id="owner_follow_up_{{$owner->LeaseId}}"/>
                                                            @endif
                                                        </td>
                                                        <td class="text-center"><a
                                                                    href="{{url( 'owner/' . $interestArea . '/' . $owner->Grantor . '/' . $isProducing)}}">{{$owner->Grantor}}</a>
                                                        </td>
                                                        <td class="text-center">{{$owner->Grantee}}</td>

                                                        <td class="text-center">{{$owner->RecordDate}}</td>
                                                        <td class="text-center">{{$owner->AreaAcres}}</td>
                                                    @else
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center">
                                                            <button class="btn btn-primary open_phone_modal"
                                                                    id="open_phone_{{$owner->LeaseId}}"
                                                                    data-target="#modal_add_phone" data-toggle="modal">
                                                                Contact Info
                                                            </button>
                                                        </td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"><a
                                                                    href="{{url( 'owner/' . $interestArea . '/' . $owner->Grantor . '/' . $isProducing)}}">{{$owner->Grantor}}</a>
                                                        </td>
                                                        <td class="text-center">{{$owner->RecordDate}}</td>
                                                        <td class="text-center"></td>
                                                        <td class="text-center"></td>
                                                    @endif
                                                </tr>
                                            @endforeach
                                            </tbody>
                                            <tfoot>
                                            <caption id="owner_table_caption">Owners</caption>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="modal fade" id="modal_open_wells">
                        <div class="modal-dialog" role="document">
                            <div style="margin-left:-150px; width:850px;" class="modal-content">
                                <div class="modal-header">
                                    <h4>Wells </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <div class="containers" style="text-align:center; margin-bottom:10px;">
                                        <h3>Sum Well Data</h3>
                                        <label for="first_month">First month in Production: </label>
                                        <span>{{$oldestDate[0]}}</span><br>

                                        <label for="last_month">Last month in Production: </label>
                                        <span>{{$latestDate[0]}}</span><br>

                                        <label for="last_month">Years of Production: </label>
                                        <span>{{$yearsOfProduction}}</span><br>

                                        <label for="total_gas">Total Gas Production: </label>
                                        <span>{{$totalGasWithComma}}</span><br>

                                        <label for="total_oil">Total Oil Production: </label>
                                        <span>{{$totalOilWithComma}}</span><br>
                                    </div>
                                    <table class="table table-hover table-responsive-md table-bordered wells_table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Well Prod Details</th>
                                            <th class="text-center">County</th>
                                            <th class="text-center">Operator</th>
                                            <th class="text-center">Current Status</th>
                                            <th class="text-center">Well Name</th>
                                            <th class="text-center">Well Number</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach ($wells as $well)
                                            <tr>
                                                <td id="{{$well->id}}" class="text-center details-control"><i
                                                            style="cursor:pointer;" class="far fa-dot-circle"></i></td>
                                                <td class="text-center">{{$well->CountyParish}}</td>
                                                <td class="text-center">{{$well->OperatorCompanyName}}</td>
                                                <td class="text-center">{{$well->WellStatus}}</td>
                                                <td class="text-center">{{$well->WellName}}</td>
                                                <td class="text-center">{{$well->WellNumber}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <br>
                                <!-- adding random comment -->
                                <div class="modal-footer">
                                    <button type="button" id="cancel_phone" class="cancel_phone_btn btn btn-success"
                                            data-dismiss="modal">Close Modal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="modal_add_phone">
                        <div style="width:650px!important;" class="modal-dialog phone_modal_dialog" role="document">
                            <div style="margin-left:-60%; margin-top:50%;" class="modal-content">
                                <div class="modal-header">
                                    <h4>Add Phone Number</h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body row">
                                    <div class="col-md-6">
                                        <label>Phone Description</label><input type="text" class="form-control"
                                                                               id="new_phone_desc" name="new_phone_desc"
                                                                               placeholder="Home, Cell, Sister, Etc."/>
                                        <label>Phone Number</label><input type="text" class="form-control"
                                                                          id="new_phone_number" name="new_phone_number"
                                                                          placeholder="(ext) 000 - 0000"/>
                                        <div class="modal-footer">
                                            <button type="button" id="submit_phone"
                                                    class="submit_phone_btn btn btn-primary">Submit #
                                            </button>
                                            <button type="button" id="cancel_phone"
                                                    class="cancel_phone_btn btn btn-success" data-dismiss="modal">Close
                                                Modal
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <span class="phone_container" id="phone_container" style="padding: 2%;"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
