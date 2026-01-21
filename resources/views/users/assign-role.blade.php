@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Assign Role to User</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-8 col-md-8">
            <div class="card o-hidden border-0 shadow-lg my-5">
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="p-5">
                                <div class="text-center">
                                    <h1 class="h4 text-gray-900 mb-4">Assign Role to: {{ $user->name }}</h1>
                                </div>
                                <form action="{{ route('users.assign-role', $user->id) }}" method="POST" class="user">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="role_id">Select Role:</label>
                                        <select name="role_id" id="role_id" class="form-control" required>
                                            <option value="">-- Select a Role --</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                                    {{ $role->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-user btn-block">
                                        Assign Role
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