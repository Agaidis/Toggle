@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Owners Page</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="offset-2 col-md-11" style="text-align: center;">
                                    <div style="background-color: lightgray;width: 67%;padding: 5%;border-radius: .5em;">
                                        <span>Assigned To:</span>
                                        <h4>{{$assignee}}</h4>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h2 style="text-align:center;">Owner: {{$ownerName}}</h2>
                                <input type="hidden" value="{{$ownerName}}" id="owner_name" />
                                <input type="hidden" value="{{$interestArea}}" id="interest_area" />
                                @if (isset($ownerLeaseData[0]) && ($interestArea == 'eagleford' || $interestArea == 'wtx' || $interestArea == 'tx'))
                                    <h3 style="text-align:center;">Address: {{$ownerLeaseData[0]->owner_address}}<br>{{$ownerLeaseData[0]->owner_city}}, {{$ownerLeaseData[0]->owner_state}}</h3>
                                @else
                                    <h3 style="text-align:center;">Address: {{$ownerLeaseData[0]->GrantorAddress}}</h3>

                                @endif
                            </div>
                            <div class="col-md-4">
                                <div class="offset-2 col-md-11" style="text-align: center;">
                                    <div style="background-color: lightgray;width: 67%;padding: 5%;border-radius: .5em;">
                                        <span>Follow-up Date:</span>
                                        <h4>{{$followUpDate}}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                             <div class="col-md-4">
                                 <h4 style="text-align:center;">Phone Numbers    <button class="btn btn-group-sm btn-success add_phone_btn" id="add_phone_{{$ownerName}}" data-target="#modal_add_phone" data-toggle="modal"><i class="fas fa-plus"></i></button></h4>
                                 <div>
                                     <table class="table table-hover table-responsive-md table-bordered" id="owner_phone_table">
                                         <thead>
                                         <tr>
                                             <th class="text-center">Phone Description</th>
                                             <th class="text-center">Phone Number</th>
                                         </tr>
                                         </thead>
                                         <tbody>
                                         @if (isset($ownerPhoneNumbers))
                                         @foreach ($ownerPhoneNumbers as $ownerPhoneNumber)
                                             <tr>
                                                 @if ($ownerPhoneNumber->soft_delete === 1)
                                                     <td class="text-center" style="color:red; font-weight:bold">{{$ownerPhoneNumber->phone_desc}}</td>
                                                     @else
                                                     <td class="text-center" style="font-weight:bold">{{$ownerPhoneNumber->phone_desc}}</td>
                                                     @endif
                                                 <td class="text-center"><a href="tel:{{$ownerPhoneNumber->phone_number}}">{{$ownerPhoneNumber->phone_number}}</a></td>
                                             </tr>
                                         @endforeach
                                             @endif
                                         </tbody>
                                     </table>
                                 </div>
                             </div>
                            <div class="col-md-3 email_ctr">
                                <h4 for="email">Email: </h4>
                                <input type="text" placeholder="Enter Email: " class="form-control" id="email" value="{{$email}}"/><br>
                                <button type="button" class="btn btn-primary" id="email_btn">Submit Email</button>
                                <div class="status-msg"></div>
                            </div>
                        </div>
                            <div class="col-md-12">
                                <h3 style="text-align:center;">Leases</h3>
                                <div>
                                    <table class="table table-hover table-responsive-md table-bordered" id="owner_lease_table">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Id</th>
                                            <th class="text-center">Lease Name</th>
                                            @if (isset($ownerLeaseData[0]))
                                                <th style="width:20%;" class="text-center">Lease Description</th>
                                                <th class="text-center">ODI</th>
                                                <th class="text-center">Lease Notes</th>
                                            @else
                                                <th style="width:20%;" class="text-center">Notes</th>
                                            @endif
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $count = 0; ?>
                                        @if (isset($ownerLeaseData))
                                        @foreach ($ownerLeaseData as $ownerLease)
                                            <tr>
                                                @if (isset($permitObj[$count]))
                                                    <td class="text-center"><?php echo $count ?> </td>
                                                    @if ($permitObj[$count]['lease_name'] != '')
                                                        <td class="text-center"><a href="{{url( 'lease-page/' . $interestArea . '/' . $ownerLease->lease_name . '/' . $isProducing . '/' .$permitObj[$count]['permit_id'])}}">{{$permitObj[$count]['lease_name']}}</a></td>
                                                    @else
                                                        <td class="text-center"><a href="{{url( 'lease-page/' . $permitObj[$count]['interest_area'] . '/' . $permitObj[$count]['lease_name'] . '/' . $isProducing . '/' .$permitObj[$count]['permit_id'])}}">{{$permitObj[$count]['lease_name']}}</a> <br>{{$permitObj[$count]['lease_name']}}</td>
                                                    @endif

                                                    @if (isset($ownerLeaseData[0]))
                                                        <td class="text-center">{{$ownerLease->lease_description}}</td>
                                                        <td class="text-center">{{$ownerLease->owner_decimal_interest}}</td>
                                                    @endif

                                                    @if ($noteArray[$count]['lease_name'] === $ownerLease->lease_name)
                                                        <td class="text-center"><div class="owner_notes" contenteditable="false">{!! $noteArray[$count]['notes'] !!}</div></td>
                                                    @else
                                                        <td class="text-center">n/a</td>
                                                    @endif
                                                @endif
                                            </tr>
                                            <?php $count++; ?>
                                        @endforeach
                                        @endif
                                        </tbody>
                                    </table>
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
    </div>
@endsection
