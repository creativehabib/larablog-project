@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Sub Categories')
@section('content')
    <livewire:admin.sub-categories-table />
@endsection
