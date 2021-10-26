@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <meta name="csrf-token" id="token" content="{{ csrf_token() }}">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body body_container">
                        <div class="row">
                            <div class="col-md-12">
                                <h3 style="text-align: center;">Upload Texas Mineral Appraisal Files</h3>
                                <form id="upload-tma-form" role="form" action="{{ route('uploadTMA') }}" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                    <div class="offset-5 col-md-4">
                                        <div class="col-md-6">
                                            <label for="tma">Upload CSV File from Enverus</label><br>
                                            <input type="file" name="tma" id="tma"/>
                                        </div><br>
                                        <div class="col-md-6">
                                            <input type="submit" value="Upload File" id="submit-btn" class="form-control btn btn-success">
                                        </div>
                                    </div><br>
                                    <div class="offset-4 col-md-4">
                                    @if(session()->has('message'))
                                        <div class="alert alert-success" style="text-align: center; font-size:20px;">
                                            {{ session()->get('message') }}
                                        </div>
                                    @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection