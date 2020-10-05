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
                        </div><br><br>
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
