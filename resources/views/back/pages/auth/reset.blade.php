@extends('back.layout.auth-layout')
@section('pageTitle', $pageTitle ?? 'Page Title here')
@section('content')
    <form class="auth-form" method="POST" action="{{ route('admin.reset.password.submit', ['token'=> $token]) }}">
        <x-form-alerts></x-form-alerts>
        @csrf
        <div class="mb-4">
            <div class="mb-3">
                <img class="rounded" src="{{ asset('assets/apple-touch-icon.png') }}" alt="" height="72">
            </div>
            <h1 class="h3"> Reset Password </h1>
        </div>
        <p class="mb-4">Enter your new password, confirm and submit</p><!-- .form-group -->
        <div class="form-group mb-4">
            <label class="d-block text-left" for="newPassword">New Password</label>
            <input type="password" id="newPassword" name="new_password" class="form-control form-control-lg" autofocus="" placeholder="Enter new password">
            @error('new_password') <span class="text-danger">{{ $message }}</span> @enderror
        </div><!-- /.form-group -->
        <!-- .form-group -->
        <div class="form-group mb-4">
            <label class="d-block text-left" for="new_password_confirmation">Confirm New Password</label>
            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control form-control-lg" placeholder="Enter confirm new password">
            @error('new_password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
        </div><!-- /.form-group -->
        <!-- .form-group -->
        <div class="form-group mb-4">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Submit</button>
        </div><!-- /.form-group -->
        <!-- sign in links -->
        <p class="py-2">
            <a href="{{ route('admin.login') }}" class="link">Sign in</a> <span class="mx-2">·</span>
            <a href="#" class="link">Sign up</a>
        </p><!-- /sign in links -->
        <!-- copyright -->
        <p class="mb-0 px-3 text-muted text-center"> © {{ date('Y') }} All Rights Reserved. <a href="#">Privacy</a> and <a href="#">Terms</a>
        </p>
    </form><!-- /.auth-form -->
    <!-- .auth-announcement -->
    <div id="announcement" class="auth-announcement" style="background-image: url(assets/images/illustration/img-1.png);">
        <div class="announcement-body">
            <h2 class="announcement-title"> How to Prepare for an Automated Future </h2><a href="#" class="btn btn-warning"><i class="fa fa-fw fa-angle-right"></i> Check Out Now</a>
        </div>
    </div><!-- /.auth-announcement -->
@endsection
