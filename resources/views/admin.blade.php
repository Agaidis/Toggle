@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Admin Area</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6" style="margin-top: 2%; display: block;">
                                <h3>Grab Permits by County - <br>Pulled based on an Approved Date of everything after April 1st, 2020 </h3>
                                <div class="col-md-6">
                                    <input class="form-control county_parish" id="county_select" placeholder="County ex. KARNES"/>
                                </div><br>
                                <div class="col-md-4">
                                    <button type="button" id="update_permit_btn" class="btn btn-primary">Get Data</button>
                                    <span class="loader"></span>
                                </div><br>
                            </div><br>
                            <div style="text-align:center;" class="col-md-offset-2 col-md-8">
                                @if(Session::has('message'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-info') }}">{{ Session::get('message') }}</p>
                                @endif
                                @if(Session::has('alert-danger'))
                                    <p class="alert {{ Session::get('alert-class', 'alert-danger') }}">{{ Session::get('alert-danger') }}</p>
                                @endif
                                <div class="messages"></div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-1 col-md-10">
                                <table class="table table-hover table-responsive-md table-bordered" id="admin_permit_table">
                                    <thead>
                                    <tr>
                                            <th class="text-center">Open Lease</th>
                                            <th class="text-center">State / County</th>
                                            <th class="text-center">Reported Operator</th>
                                            <th class="text-center">Lease Name</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
