@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ??  'Page title here')
@section('content')
    @livewire('admin.settings')
@endsection

