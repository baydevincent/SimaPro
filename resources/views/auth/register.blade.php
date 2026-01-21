@extends('layouts.auth')

@section('main-content')
<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-xl-5 col-lg-6 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <!-- Nested Row within Card Body -->
                    <div class="p-5">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-user-plus fa-3x text-primary"></i>
                            </div>
                            <h1 class="h4 text-gray-900 font-weight-bold">Create an Account!</h1>
                            <p class="text-muted">Register to get started</p>
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

                        <form method="POST" action="{{ route('register') }}" class="user">
                            @csrf

                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="name" placeholder="{{ __('First Name') }}" value="{{ old('name') }}" required autofocus>
                            </div>

                            <div class="form-group mb-3">
                                <input type="text" class="form-control form-control-user" name="last_name" placeholder="{{ __('Last Name') }}" value="{{ old('last_name') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <input type="email" class="form-control form-control-user" name="email" placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <input type="password" class="form-control form-control-user" name="password" placeholder="{{ __('Password') }}" required>
                            </div>

                            <div class="form-group mb-3">
                                <input type="password" class="form-control form-control-user" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-user btn-block font-weight-bold">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </form>

                        <hr>

                        <div class="text-center">
                            <a class="small" href="{{ route('login') }}">
                                {{ __('Already have an account? Login!') }}
                            </a>
                        </div>

                        <div class="text-center mt-3">
                            <h6 class="text-gray-600 small mb-0">SimaPro - Construction Management System</h6>
                            <p class="text-muted small mb-0">© {{ date('Y') }} All Rights Reserved</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
