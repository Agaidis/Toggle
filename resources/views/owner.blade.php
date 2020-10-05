@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Owners Page</div>
                    <div class="card-body">
                        <h2 style="text-align:center;">Owner: {{$ownerName}}</h2>
                        <input type="hidden" value="{{$ownerName}}" id="owner_name" />
                        @if (isset($ownerLeaseData[0]) && ($interestArea == 'eagleford' || $interestArea == 'wtx' || $interestArea == 'tx'))
                        <h3 style="text-align:center;">Address: {{$ownerLeaseData[0]->owner_address}}<br>{{$ownerLeaseData[0]->owner_city}}, {{$ownerLeaseData[0]->owner_state}}</h3>
                        @else
                            <h3 style="text-align:center;">Address: {{$ownerLeaseData[0]->GrantorAddress}}</h3>

                        @endif
                        <div class="row">
                             <div class="col-md-4">
                                 <h3 style="text-align:center;">Phone Numbers</h3>
                                 <div>
                                     <table class="table table-hover table-responsive-md table-bordered" id="owner_phone_table">
                                         <thead>
                                         <tr>
                                             <th class="text-center">Phone Description</th>
                                             <th class="text-center">Phone Number</th>
                                         </tr>
                                         </thead>
                                         <tbody>
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
                                            @if (isset($ownerLeaseData[0]) && ($interestArea == 'eagleford' || $interestArea == 'wtx' || $interestArea == 'tx'))
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
                                                        <td class="text-center"><a href="{{url( 'lease-page/' . $interestArea . '/' . $ownerLease->lease_name . '/' . $isProducing . '/' .$permitObj[$count]['permit_id'])}}">{{$ownerLease->lease_name}}</a></td>
                                                    @else
                                                        <td class="text-center">{{$ownerLease->lease_name}}</td>
                                                    @endif

                                                    @if (isset($ownerLeaseData[0]) && ($interestArea == 'eagleford' || $interestArea == 'wtx' || $interestArea == 'tx'))
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

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
