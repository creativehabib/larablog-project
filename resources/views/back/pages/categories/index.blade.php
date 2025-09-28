@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Categories')
@section('content')
    <livewire:admin.categories-table />
@endsection
