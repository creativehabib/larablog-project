@extends('back.layout.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Page title here')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Permission <a href="{{url('permissions/create')}}" class="btn btn-primary float-end">Add Permission</a> </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
