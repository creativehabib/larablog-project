@extends('back.layout.pages-layout')
@section('pageTitle', $pageTitle ?? 'Page title here')
@section('content')
    <!-- .page-title-bar -->
    <header class="page-title-bar">
        <!-- page title stuff goes here -->
        <h1 class="page-title"> Dashboard </h1>
    </header><!-- /.page-title-bar -->
    <!-- .page-section -->
    <div class="page-section">
        <!-- .section-block -->
        <div class="section-block">
            <!-- page content goes here -->
            <p> Hello world! </p>
        </div><!-- /.section-block -->
    </div><!-- /.page-section -->
@endsection
