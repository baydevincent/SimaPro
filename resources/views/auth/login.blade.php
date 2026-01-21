@extends('layouts.auth')

@section('main-content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-hard-hat fa-3x text-primary"></i>
                            </div>
                            <h1 class="h4 text-gray-900 font-weight-bold">Welcome Back!</h1>
                            <p class="text-muted">Sign in to your account</p>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger border-left-danger" role="alert">
                                <ul class="pl-4 my-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="user">
                            @csrf

                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="username" placeholder="{{ __('Username') }}" value="{{ old('username') }}" required autofocus>
                            </div>

                            <div class="form-group mb-3">
                                <input type="password" class="form-control form-control-user" name="password" placeholder="{{ __('Password') }}" required>
                            </div>

                            <div class="form-group d-flex justify-content-between align-items-center">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="remember">{{ __('Remember Me') }}</label>
                                </div>

                                <!-- @if (Route::has('password.request'))
                                    <a class="small" href="{{ route('password.request') }}">
                                        {{ __('Forgot Password?') }}
                                    </a>
                                @endif -->
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-user btn-block font-weight-bold">
                                    {{ __('Login') }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="text-center">
                            <h6 class="text-gray-600 small mb-0">SimaPro - Mia Tehnik</h6>
                            <p class="text-muted small mb-0">© {{ date('Y') }} All Rights Reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
