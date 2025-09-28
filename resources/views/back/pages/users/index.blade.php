@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'User Management')
@section('content')
    <livewire:admin.users-table />
@endsection
