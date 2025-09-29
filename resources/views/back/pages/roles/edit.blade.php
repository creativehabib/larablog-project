@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Edit Role')
@section('content')
    <header class="page-title-bar">
        <h1 class="page-title">Edit Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-link">&larr; Back to roles</a>
    </header>

    <div class="page-section">
        <div class="card card-fluid">
            <div class="card-body">
                <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('back.pages.roles._form', ['submitLabel' => __('Update Role')])
                </form>
            </div>
        </div>
    </div>
@endsection
