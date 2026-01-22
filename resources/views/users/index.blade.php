@extends('layouts.admin')

@section('main-content')
<div class="container-fluid">
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 mb-2 mb-md-0">Manage Users</h1>
        <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50"></i> Buat User
        </a>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">User List</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th width="280px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @if($user->roles->count() > 0)
                                            {{ $user->roles->first()->display_name }}
                                        @else
                                            <span class="badge badge-secondary">No Role Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        <!-- <a class="btn btn-primary btn-sm" href="{{ route('users.assign-role-form', $user->id) }}" title="Assign Role">
                                            <i class="fas fa-user-tag"></i>
                                        </a> -->
                                        <a class="btn btn-warning btn-sm mb-1 mb-md-0" href="{{ route('users.edit', $user->id) }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection