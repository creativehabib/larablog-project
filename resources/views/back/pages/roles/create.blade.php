@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Create Role')
@section('content')
    <header class="page-title-bar">
        <h1 class="page-title">Create Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-link">&larr; Back to roles</a>
    </header>

    <div class="page-section">
        <div class="card card-fluid">
            <div class="card-body">
                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    @include('back.pages.roles._form', ['submitLabel' => __('Create Role')])
                </form>
            </div>
        </div>
    </div>
@endsection
