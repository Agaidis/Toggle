@extends('layouts.app')
@section('content')
    <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body body_container">
                        <h2 class="titles">Welcome to Toggle!</h2>

                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection