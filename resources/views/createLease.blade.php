@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Create Lease</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST"  action={{ route('createLease') }} >
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="row">
                            <div class="col-md-12" style="margin-top: 2%; text-align:center; display: block;">
                                <h3>Create a new Lease</h3>
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                            </div><br>
                        </div>
                        <div class="row">
                            <div class="offset-4 col-md-4">
                                <label for="county">Select County: </label>
                                <select class="form-control" name="county">
                                    <option selected disabled value="none">Select County</option>
                                    @foreach ($counties as $county)
                                    <option value="{{$county->county_parish}}">{{$county->county_parish}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="offset-4 col-md-4">
                                <label class="labels">Lease Name(s)</label>:
                                <select id="create_lease_name_select" class="form-control" name="leaseName" multiple="multiple">
                                    @foreach ($selectLeases as $selectLease)
                                        <option value="{{$selectLease->lease_name}}">{{$selectLease->lease_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Survey</label>:
                                   <input class="form-control" placeholder="Survey" type="text" id="survey" name="survey" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Abstract</label>:
                                    <input class="form-control" placeholder="Abstract" type="text" id="abstract" name="abstract" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Operator</label>:
                                    <input class="form-control" placeholder="Operator" type="text" id="operator" name="operator" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Block</label>:
                                    <input class="form-control" placeholder="Block" type="text" id="block" name="block" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Drill Type</label>:
                                    <select class="form-control" id="drill_type" name="drill_type">
                                        <option value="H">H</option>
                                        <option value="V">V</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Well Type</label>:
                                    <select class="form-control" id="well_type" name="well_type">
                                        <option value="OIL">OIL</option>
                                        <option value="GAS">GAS</option>
                                        <option value="UNKNOWN">UNKNOWN</option>
                                        <option value="INJECTION">INJECTION</option>
                                        <option value="DISPOSAL">DISPOSAL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="offset-4 col-md-4">
                                    <label class="labels">Permit Status</label>:
                                    <select class="form-control" id="permit_status" name="permit_status">
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                            </div>

                            <br><br>
                            <div class="row">
                            <div class="offset-4 col-md-2">
                                <button type="submit" class="btn btn-primary">Create Lease</button>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection