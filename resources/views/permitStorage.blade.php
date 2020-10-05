@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body body_container">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 style="text-align: center;">Permit Storage</h3>
                                <div class="row">
                                    <div class="offset-1 col-md-10">
                                        <table class="table table-hover table-responsive-md table-bordered stored_permit_table" id="stored_permit_table">
                                            <thead>
                                            <tr>
                                                <th class="text-center">Bring Lease Back</th>
                                                <th class="text-center">State / County</th>
                                                <th class="text-center">Reported Operator</th>
                                                <th class="text-center">Lease Name</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @if (isset($permits) && !$permits->isEmpty())
                                                @foreach ($permits as $permit)
                                                    <?php $approvedDate = explode('T', $permit->approved_date)?>
                                                    <tr class="permit_row" id="permit_row_{{$permit->permit_id}}">
                                                        <td class="text-center"><button type="button" class="store_button btn btn-primary" id="store_button_{{$permit->permit_id}}_{{$permit->lease_name}}">Back to MMP</button></td>
                                                        <td class="text-center">{{$permit->county_parish}}</td>
                                                        <td class="text-center">{{$permit->reported_operator}}</td>
                                                        <td class="text-center"><a href="{{url( 'lease-page/' . $permit->lease_name . '/' . $permit->reported_operator . '/' . $permit->id)}}">{{$permit->lease_name}}</a></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                            <tfoot>
                                            <caption class="lease_table_caption">Stored Permits </caption>
                                            </tfoot>
                                        </table>
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