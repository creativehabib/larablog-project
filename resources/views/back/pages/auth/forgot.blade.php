@extends('back.layout.auth-layout')
@section('pageTitle', $pageTitle ?? 'Page title here')
@section('content')
    <form class="auth-form" method="POST" action="{{ route('admin.send.password.reset.link') }}">
        <x-form-alerts></x-form-alerts>
        @csrf
        <div class="text-center mb-4">
            <div class="mb-4">
                <img class="rounded" src="{{ asset('assets/apple-touch-icon.png') }}" alt="" height="72">
            </div>
            <h1 class="h3"> Reset Your Password </h1>
        </div>
        <p class="mb-4">Enter your email address to reset your password</p><!-- .form-group -->
        <div class="form-group mb-4">
            <label class="d-block text-left" for="inputEmail">Email</label>
            <input type="email" id="inputEmail" class="form-control form-control-lg" value="{{old('email')}}" name="email" placeholder="Enter email" autofocus="">
            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
        </div><!-- /.form-group -->
        <!-- actions -->
        <div class="d-block d-md-inline-block mb-2">
            <button class="btn btn-lg btn-block btn-primary" type="submit">Reset Password</button>
        </div>
        <div class="d-block d-md-inline-block">
            <a href="{{ route('admin.login') }}" class="btn btn-block btn-light">Return to signin</a>
        </div>
        <footer class="mt-4 text-muted text-center"> © {{ date('Y') }} All Rights Reserved. <a href="#">Privacy</a> and <a href="#">Terms</a>
        </footer>
    </form><!-- /.auth-form -->

    <div id="announcement" class="auth-announcement" style="background-image: url({{ asset('assets/images/illustration/img-1.png') }});">
        <div class="announcement-body">
            <h2 class="announcement-title"> How to Prepare for an Automated Future </h2>
            <a href="#" class="btn btn-warning"><i class="fa fa-fw fa-angle-right"></i> Check Out Now</a>
        </div>
    </div>
@endsection
