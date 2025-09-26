@extends('back.layout.auth-layout')
@section('pageTitle', $pageTitle ?? 'Page title here')
@section('content')
    <form class="auth-form" method="POST" action="{{ route('admin.login.submit') }}">
        <x-form-alerts></x-form-alerts>
        @csrf
        <div class="mb-4">
            <div class="mb-3">
                <img class="rounded" src="{{ asset('assets/apple-touch-icon.png') }}" alt="" height="72">
            </div>
            <h1 class="h3"> Sign In </h1>
        </div>
        <p class="text-left mb-4"> Don't have an account? <a href="">Create One</a></p>

        <div class="form-group mb-4 text-left">
            <label class="d-block text-left" for="inputUser">Username</label>
            <input type="text" id="inputUser" class="form-control form-control-lg" placeholder="Username/Email"
                   name="login_id" value="{{ old('login_id') }}" autofocus>
            @error('login_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>

        <div class="form-group mb-4 text-left">
            <label class="d-block text-left" for="inputPassword">Password</label>
            <input type="password" id="inputPassword" name="password" value="{{ old('password') }}" class="form-control form-control-lg">
            @error('password')<span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <div class="form-group mb-4">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Sign In</button>
        </div>

        <div class="form-group text-center">
            <div class="custom-control custom-control-inline custom-checkbox">
                <input type="checkbox" class="custom-control-input" id="remember-me">
                <label class="custom-control-label" for="remember-me">Keep me signed in</label>
            </div>
        </div>

        <p class="py-2">
            <a href="{{ route('admin.forgot') }}" class="link">Forgot Password?</a>
        </p>

        <p class="mb-0 px-3 text-muted text-center"> © {{ date('Y') }} All Rights Reserved. </p>
    </form>

    <div id="announcement" class="auth-announcement" style="background-image: url({{ asset('assets/images/illustration/img-1.png') }});">
        <div class="announcement-body">
            <h2 class="announcement-title"> How to Prepare for an Automated Future </h2>
            <a href="#" class="btn btn-warning"><i class="fa fa-fw fa-angle-right"></i> Check Out Now</a>
        </div>
    </div>
@endsection
