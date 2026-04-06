@extends('layouts.admin')

@section('main-content')
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between text-center">
        <h3 class="m-0 font-weight-bold text-primary ">Edit User: {{ $user->name }}</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('users.update', $user->id) }}" method="POST" class="user">
            @csrf
            @method('PUT')

            <div class="form-group row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <label>Nama Depan:</label>
                    <input type="text" name="name" class="form-control form-control-user" 
                        placeholder="First Name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-sm-6">
                    <label>Nama Belakang:</label>
                    <input type="text" name="last_name" class="form-control form-control-user" 
                        placeholder="Last Name" value="{{ old('last_name', $user->last_name) }}">
                    @error('last_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" class="form-control form-control-user" 
                    placeholder="Username" value="{{ old('username', $user->username) }}" required>
                @error('username')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-group">
                <label>Email:</label>
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
                            <option value="">Pilih Role</option>
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
                
                <div class="row mt-4">
                    <div class="col-sm-12 text-right">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-user mr-2">
                            <i class="fas fa-times mr-1"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary btn-user">
                            <i class="fas fa-save mr-1"></i> Update
                        </button>
                    </div>
                </div>
        </form>
    </div>
</div>
@endsection