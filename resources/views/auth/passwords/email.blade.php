@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')

<div class="page-ath-form">

    <h2 class="page-ath-heading">Reset password <span>If you forgot your password, well, then we'll email you instructions to reset your password.</span></h2>
    @include('layouts.messages')
    <form method="POST" action="{{ route('password.email') }}" class="forgot-pass-form validate validate-modern">
        @csrf
        <div class="input-item">
            <input type="email" placeholder="Your Email Address" name="email" value="{{ old('email') }}" class="input-bordered" required>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
            </div>
            <div>
                <a href="{{ route('login') }}">Return to login</a>
            </div>
        </div>
        <div class="gaps-0-5x"></div>
    </form>

</div>
@endsection
