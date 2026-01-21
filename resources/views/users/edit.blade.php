@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit User</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-8 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Edit User: {{ $user->name }}</h1>
                                </div>
                                <form action="{{ route('users.update', $user->id) }}" method="POST" class="user">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="text" name="name" class="form-control form-control-user" 
                                                   placeholder="First Name" value="{{ old('name', $user->name) }}" required>
                                            @error('name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="text" name="last_name" class="form-control form-control-user" 
                                                   placeholder="Last Name" value="{{ old('last_name', $user->last_name) }}">
                                            @error('last_name')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="username" class="form-control form-control-user" 
                                               placeholder="Username" value="{{ old('username', $user->username) }}" required>
                                        @error('username')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group">
                                        <input type="email" name="email" class="form-control form-control-user" 
                                               placeholder="Email Address" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-sm-6 mb-3 mb-sm-0">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                   placeholder="Password (leave blank to keep current)">
                                            @error('password')
                                                <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6">
                                            <input type="password" name="password_confirmation" class="form-control form-control-user"
                                                   placeholder="Repeat Password">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Select Role:</label>
                                        <select name="role_id" class="form-control">
                                            <option value="">-- Select a Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" 
                                                        {{ old('role_id', $user->roles->first() ? $user->roles->first()->id : '') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Update User
                                    </button>
                                    <hr>
                                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-user btn-block">
                                        Cancel
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection