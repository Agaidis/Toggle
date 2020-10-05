@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body body_container">
                        <div id="dashboard_btn_container" class="col-md-4">
                            <div class="button_panel">
                                @if ($userRole == 'admin')
                                    <select class="form-control" id="wellbore_user_change">
                                        @if ($userId == 3)
                                            <option value="0">Select User</option>
                                            <option selected value="3">Billy</option>
                                            <option value="68">Justus</option>
                                        @elseif ($userId == 68)
                                            <option value="0">Select User</option>
                                            <option value="3">Billy</option>
                                            <option selected value="68">Justus</option>
                                        @else
                                            <option selected value="0">Select User</option>
                                            <option value="3">Billy</option>
                                            <option value="68">Justus</option>
                                        @endif
                                    </select>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12 titles">
                            <h1>Wellbore</h1>
                            <h5>4 - Done Deal</h5>
                            <h5>3 - Signed and being Processed</h5>
                            <h5>2 - We have sent an Offer</h5>
                            <h5>1 - They are a lead....</h5>
                        </div>
                        <input type="hidden" id="owner_id"/>

                        <div>
                            <ul class="nav nav-pills">
                                <li  class="nav-item" id="interest_tab_texas"><a class="nav-link active" data-toggle="tab" href="#texas_area">Texas</a></li>
                                <li class="nav-item" id="interest_tab_new_mexico"><a class="nav-link" data-toggle="tab" href="#new_mexico_area">New Mexico</a></li>
                            </ul>

                            <div class="tab-content">

                                <input type="hidden" id="current_interest_area" />
                                <!-- EAGLE INTEREST AREA TAB -->
                                <div class="tab-pane active in" id="texas_area">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-hover table-responsive-md table-bordered wellbore high_priority_wellbore_table"
                                                       style="width:1475px;">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">See Notes</th>
                                                        <th class="text-center">Assignee</th>
                                                        <th class="text-center">Wellbore Type</th>
                                                        <th class="text-center" style="width:250px;">Contact</th>
                                                        <th class="text-center">Follow-Up</th>
                                                        <th class="text-center">Owner</th>
                                                        <th class="text-center">ODI</th>
                                                        <th class="text-center">% Type</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    @foreach ($highPriorityProspects as $highPriorityProspect)
                                                        <input type="hidden" id="texas_high_interest_area" value="{{$highPriorityProspect->interest_area}}" />

                                                        @if ($highPriorityProspect->follow_up_date == date('Y-m-d') || $highPriorityProspect->follow_up_date > date('Y-m-d') || $highPriorityProspect->follow_up_date === NULL)
                                                            <tr class="owner_row"
                                                                id="owner_row_{{$highPriorityProspect->id}}">
                                                        @else
                                                            <tr class="owner_row" style="background-color: #f59278;"
                                                                id="owner_row_{{$highPriorityProspect->id}}">
                                                                @endif
                                                                @if (isset($highPriorityProspect->lease_name))
                                                                    <input type="hidden"
                                                                           id="lease_name_{{$highPriorityProspect->id}}"
                                                                           value="{{$highPriorityProspect->lease_name}}"/>
                                                                @else
{{--                                                                    <input type="hidden"--}}
{{--                                                                           id="lease_name_{{$highPriorityProspect->id}}"--}}
{{--                                                                           value="{{$operator}}"/>--}}
                                                                @endif

                                                                <input type="hidden"
                                                                       value="{{$highPriorityProspect->interest_area}}"
                                                                       id="interest_area_{{$highPriorityProspect->interest_area}}"/>

                                                                <td id="id_{{$highPriorityProspect->id}}"
                                                                    class="text-center wellbore-details-control"><i
                                                                            style="cursor:pointer;"
                                                                            class="far fa-dot-circle"></i></td>
                                                                <td class="text-center">
                                                                    @if ($highPriorityProspect->assignee == '')
                                                                        <select class="form-control owner_assignee"
                                                                                id="assignee_{{$highPriorityProspect->id}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee assigned_style"
                                                                                        id="assignee_{{$highPriorityProspect->id}}">
                                                                                    @endif

                                                                                    <option value="0" selected>Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($highPriorityProspect->assignee == $user->id)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <select class="form-control wellbore_dropdown"
                                                                            id="wellbore_dropdown_{{$highPriorityProspect->id}}">
                                                                        @if ($highPriorityProspect->wellbore_type == 1)
                                                                            <option value="0">None</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspect->wellbore_type}}">
                                                                                1
                                                                            </option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspect->wellbore_type == 2)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspect->wellbore_type}}">
                                                                                2
                                                                            </option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspect->wellbore_type == 3)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspect->wellbore_type}}">
                                                                                3
                                                                            </option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspect->wellbore_type == 4)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspect->wellbore_type}}">
                                                                                4
                                                                            </option>
                                                                        @else
                                                                            <option selected value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary add_phone_btn"
                                                                            id="add_phone_{{$highPriorityProspect->id}}"
                                                                            data-target="#modal_add_phone"
                                                                            data-toggle="modal">Contact Info
                                                                    </button>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($highPriorityProspect->follow_up_date != '')
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$highPriorityProspect->id}}"
                                                                                value="{{date('M j, Y', strtotime($highPriorityProspect->follow_up_date))}}"/>
                                                                    @else
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$highPriorityProspect->id}}"/>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"><a
                                                                            href="{{url( 'owner/' . $highPriorityProspect->interest_area . '/' . $highPriorityProspect->owner . '/' . 'producing')}}">{{$highPriorityProspect->owner}}</a><br>{{$highPriorityProspect->owner_address}}
                                                                    <br>{{$highPriorityProspect->owner_city}}
                                                                    , {{$highPriorityProspect->owner_zip}}</td>
                                                                <td class="text-center">{{$highPriorityProspect->owner_decimal_interest}}</td>
                                                                <td class="text-center">{{$highPriorityProspect->owner_interest_type}}</td>

                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <caption class="lease_table_caption">High Priority Prospects
                                                    </caption>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-hover table-responsive-md table-bordered wellbore low_priority_wellbore_table"
                                                       style="width:1475px;">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">See Notes</th>
                                                        <th class="text-center">Assignee/Follow-Up</th>
                                                        <th class="text-center">Wellbore Type</th>
                                                        <th class="text-center" style="width:250px;">Contact</th>
                                                        <th class="text-center">Follow-Up</th>
                                                        <th class="text-center">Owner</th>
                                                        <th class="text-center">ODI</th>
                                                        <th class="text-center">% Type</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($owners as $owner)
                                                        <input type="hidden" id="texas_low_interest_area" value="{{$owner->interest_area}}" />
                                                        @if ($owner->follow_up_date == date('Y-m-d') || $owner->follow_up_date > date('Y-m-d') || $owner->follow_up_date === NULL)
                                                            <tr class="owner_row" id="owner_row_{{$owner->id}}">
                                                        @else
                                                            <tr class="owner_row" style="background-color: #f59278;"
                                                                id="owner_row_{{$owner->id}}">
                                                                @endif
                                                                @if (isset($owner->lease_name))
                                                                    <input type="hidden" id="lease_name_{{$owner->id}}"
                                                                           value="{{$owner->lease_name}}"/>
                                                                @else
{{--                                                                    <input type="hidden" id="lease_name_{{$owner->id}}"--}}
{{--                                                                           value="{{$operator}}"/>--}}
                                                                @endif

                                                                <input type="hidden" value="{{$owner->interest_area}}"
                                                                       id="interest_area_{{$owner->interest_area}}"/>

                                                                <td id="id_{{$owner->id}}"
                                                                    class="text-center wellbore-details-control"><i
                                                                            style="cursor:pointer;"
                                                                            class="far fa-dot-circle"></i></td>
                                                                <td class="text-center">
                                                                    @if ($owner->assignee == '')
                                                                        <select class="form-control owner_assignee"
                                                                                id="assignee_{{$owner->id}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee assigned_style"
                                                                                        id="assignee_{{$owner->id}}">
                                                                                    @endif
                                                                                    <option selected value="0">Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($owner->assignee == $user->id)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <select class="form-control wellbore_dropdown"
                                                                            id="wellbore_dropdown_{{$owner->id}}">
                                                                        @if ($owner->wellbore_type == 1)
                                                                            <option value="0">None</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore_type}}">1
                                                                            </option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore_type == 2)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore_type}}">2
                                                                            </option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore_type == 3)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore_type}}">3
                                                                            </option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore_type == 4)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore_type}}">4
                                                                            </option>
                                                                        @else
                                                                            <option selected value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary add_phone_btn"
                                                                            id="add_phone_{{$owner->id}}"
                                                                            data-target="#modal_add_phone"
                                                                            data-toggle="modal">Contact Info
                                                                    </button>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($owner->follow_up_date != '')
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$owner->id}}"
                                                                                value="{{date('M j, Y', strtotime($owner->follow_up_date))}}"/>
                                                                    @else
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$owner->id}}"/>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"><a
                                                                            href="{{url( 'owner/' . $owner->interest_area . '/' . $owner->owner . '/' . 'producing')}}">{{$owner->owner}}</a><br>{{$owner->owner_address}}
                                                                    <br>{{$owner->owner_city}}, {{$owner->owner_zip}}
                                                                </td>
                                                                <td class="text-center">{{$owner->owner_decimal_interest}}</td>
                                                                <td class="text-center">{{$owner->owner_interest_type}}</td>
                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <caption class="lease_table_caption">Prospects</caption>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- New Mexico INTEREST AREA TAB -->
                                <div class="tab-pane fade" id="new_mexico_area">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-hover table-responsive-md table-bordered wellbore high_priority_wellbore_tableNM"
                                                       style="width:1475px;">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">See Notes</th>
                                                        <th class="text-center">Assignee</th>
                                                        <th class="text-center">Wellbore Type</th>
                                                        <th class="text-center" style="width:250px;">Contact</th>
                                                        <th class="text-center">Follow-Up</th>
                                                        <th class="text-center">Grantor</th>

                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($highPriorityProspectsNM as $highPriorityProspectNM)
                                                        <input type="hidden" id="nm_high_interest_area" value="{{$highPriorityProspectNM->interest_area}}" />
                                                        @if ($highPriorityProspectNM->follow_up_date == date('Y-m-d') || $highPriorityProspectNM->follow_up_date > date('Y-m-d') || $highPriorityProspectNM->follow_up_date === NULL)
                                                            <tr class="owner_row"
                                                                id="owner_row_{{$highPriorityProspectNM->LeaseId}}">
                                                        @else
                                                            <tr class="owner_row" style="background-color: #f59278;"
                                                                id="owner_row_{{$highPriorityProspectNM->LeaseId}}">
                                                                @endif

                                                                <input type="hidden"
                                                                       id="lease_name_{{$highPriorityProspectNM->LeaseId}}"
                                                                       value="{{$highPriorityProspectNM->lease_name}}"/>
                                                                <input type="hidden"
                                                                       value="{{$highPriorityProspectNM->interest_area}}"
                                                                       id="interest_area_{{$highPriorityProspectNM->interest_area}}"/>


                                                                <td id="id_{{$highPriorityProspectNM->LeaseId}}"
                                                                    class="text-center wellbore-details-control"><i
                                                                            style="cursor:pointer;"
                                                                            class="far fa-dot-circle"></i></td>
                                                                <td class="text-center">
                                                                    @if ($highPriorityProspectNM->assignee == '')
                                                                        <select class="form-control owner_assignee"
                                                                                id="assignee_{{$highPriorityProspectNM->LeaseId}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee assigned_style"
                                                                                        id="assignee_{{$highPriorityProspectNM->LeaseId}}">
                                                                                    @endif

                                                                                    <option value="0" selected>Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($highPriorityProspectNM->assignee == $user->id)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <select class="form-control wellbore_dropdown"
                                                                            id="wellbore_dropdown_{{$highPriorityProspectNM->LeaseId}}">
                                                                        @if ($highPriorityProspectNM->wellbore == 1)
                                                                            <option value="0">None</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspectNM->wellbore}}">
                                                                                1
                                                                            </option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspectNM->wellbore == 2)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspectNM->wellbore}}">
                                                                                2
                                                                            </option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspectNM->wellbore == 3)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspectNM->wellbore}}">
                                                                                3
                                                                            </option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($highPriorityProspectNM->wellbore == 4)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option selected
                                                                                    value="{{$highPriorityProspectNM->wellbore}}">
                                                                                4
                                                                            </option>
                                                                        @else
                                                                            <option selected value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary add_phone_btn"
                                                                            id="add_phone_{{$highPriorityProspectNM->LeaseId}}"
                                                                            data-target="#modal_add_phone"
                                                                            data-toggle="modal">Contact Info
                                                                    </button>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($highPriorityProspectNM->follow_up_date != '')
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$highPriorityProspectNM->LeaseId}}"
                                                                                value="{{date('M j, Y', strtotime($highPriorityProspectNM->follow_up_date))}}"/>
                                                                    @else
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$highPriorityProspectNM->LeaseId}}"/>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"><a
                                                                            href="{{url( 'owner/' . $highPriorityProspectNM->interest_area . '/' . $highPriorityProspectNM->Grantor . '/' . 'non-producing')}}">{{$highPriorityProspectNM->Grantor}}</a>
                                                                </td>

                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <caption class="lease_table_caption">High Priority Prospects
                                                    </caption>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="row">
                                        <div class="col-md-12">
                                            <div style="overflow-x:auto;">
                                                <table class="table table-hover table-responsive-md table-bordered wellbore low_priority_wellbore_tableNM"
                                                       style="width:1475px;">
                                                    <thead>
                                                    <tr>
                                                        <th class="text-center">See Notes</th>
                                                        <th class="text-center">Assignee/Follow-Up</th>
                                                        <th class="text-center">Wellbore Type</th>
                                                        <th class="text-center" style="width:250px;">Contact</th>
                                                        <th class="text-center">Follow-Up</th>
                                                        <th class="text-center">Grantor</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach ($ownersNM as $owner)
                                                        <input type="hidden" id="nm_interest_area" value="{{$owner->interest_area}}" />

                                                        @if ($owner->follow_up_date == date('Y-m-d') || $owner->follow_up_date > date('Y-m-d') || $owner->follow_up_date === NULL)
                                                            <tr class="owner_row" id="owner_row_{{$owner->LeaseId}}">
                                                        @else
                                                            <tr class="owner_row" style="background-color: #f59278;"
                                                                id="owner_row_{{$owner->LeaseId}}">
                                                                @endif
                                                                <input type="hidden" value="{{$owner->interest_area}}"
                                                                       id="interest_area_{{$owner->interest_area}}"/>

                                                                <input type="hidden" id="lease_name_{{$owner->LeaseId}}"
                                                                       value="{{$owner->lease_name}}"/>

                                                                <td id="id_{{$owner->LeaseId}}"
                                                                    class="text-center wellbore-details-control"><i
                                                                            style="cursor:pointer;"
                                                                            class="far fa-dot-circle"></i></td>
                                                                <td class="text-center">
                                                                    @if ($owner->assignee == '')
                                                                        <select class="form-control owner_assignee"
                                                                                id="assignee_{{$owner->LeaseId}}">
                                                                            @else
                                                                                <select class="form-control owner_assignee assigned_style"
                                                                                        id="assignee_{{$owner->LeaseId}}">
                                                                                    @endif
                                                                                    <option selected value="0">Select a
                                                                                        User
                                                                                    </option>
                                                                                    @foreach ($users as $user)
                                                                                        @if ($owner->assignee == $user->id)
                                                                                            <option selected
                                                                                                    value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @else
                                                                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <select class="form-control wellbore_dropdown"
                                                                            id="wellbore_dropdown_{{$owner->LeaseId}}">
                                                                        @if ($owner->wellbore == 1)
                                                                            <option value="0">None</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore}}">1
                                                                            </option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore == 2)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore}}">2
                                                                            </option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore == 3)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore}}">3
                                                                            </option>
                                                                            <option value="4">4</option>
                                                                        @elseif ($owner->wellbore == 4)
                                                                            <option value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option selected
                                                                                    value="{{$owner->wellbore}}">4
                                                                            </option>
                                                                        @else
                                                                            <option selected value="0">None</option>
                                                                            <option value="1">1</option>
                                                                            <option value="2">2</option>
                                                                            <option value="3">3</option>
                                                                            <option value="4">4</option>
                                                                        @endif
                                                                    </select>
                                                                </td>
                                                                <td class="text-center">
                                                                    <button class="btn btn-primary add_phone_btn"
                                                                            id="add_phone_{{$owner->LeaseId}}"
                                                                            data-target="#modal_add_phone"
                                                                            data-toggle="modal">Contact Info
                                                                    </button>
                                                                </td>
                                                                <td class="text-center">
                                                                    @if ($owner->follow_up_date != '')
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$owner->LeaseId}}"
                                                                                value="{{date('M j, Y', strtotime($owner->follow_up_date))}}"/>
                                                                    @else
                                                                        <i class="fas fa-calendar-alt"></i> <input
                                                                                class="form-control wellbore_owner_follow_up"
                                                                                id="owner_follow_up_{{$owner->LeaseId}}"/>
                                                                    @endif
                                                                </td>
                                                                <td class="text-center"><a
                                                                            href="{{url( 'owner/' . $owner->interest_area . '/' . $owner->Grantor . '/' . 'non-producing')}}">{{$owner->Grantor}}</a>
                                                                </td>

                                                            </tr>
                                                            @endforeach
                                                    </tbody>
                                                    <tfoot>
                                                    <caption class="lease_table_caption">Prospects</caption>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
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
                                    <div class="current_phones modal-body row">
                                        <div class="col-md-6">
                                            <label>Phone Description</label><input type="text" class="form-control"
                                                                                   id="new_phone_desc"
                                                                                   name="new_phone_desc"
                                                                                   placeholder="Home, Cell, Sister, Etc."/>
                                            <label>Phone Number</label><input type="text" class="form-control"
                                                                              id="new_phone_number"
                                                                              name="new_phone_number"
                                                                              placeholder="(ext) 000 - 0000"/>
                                            <div class="modal-footer">
                                                <button type="button" id="submit_phone"
                                                        class="wellbore_submit_phone_btn btn btn-primary">Submit #
                                                </button>
                                                <button type="button" id="cancel_phone"
                                                        class="cancel_phone_btn btn btn-success" data-dismiss="modal">
                                                    Close Modal
                                                </button>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <span class="phone_container" id="phone_container"
                                                  style="padding: 2%;"></span>
                                        </div>
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