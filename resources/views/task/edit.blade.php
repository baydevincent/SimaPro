@extends('layouts.admin')
@section('title','Edit Task')

@section('main-content')
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Edit Task</h6>
    </div>
    
    <div class="card-body">
        <!-- Display success or error messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form action="{{ route('task.update', $task->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="nama_task">Nama Task</label>
                <input type="text" name="nama_task" class="form-control @error('nama_task') is-invalid @enderror" 
                       id="nama_task" value="{{ old('nama_task', $task->nama_task) }}" required>
                @error('nama_task')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            @auth
                @if(Auth::user()->hasRole('administrator'))
                <div class="form-group">
                    <label for="bobot_rupiah">Bobot Rupiah</label>
                    <input type="number" name="bobot_rupiah" class="form-control @error('bobot_rupiah') is-invalid @enderror" 
                           id="bobot_rupiah" value="{{ old('bobot_rupiah', $task->bobot_rupiah) }}" required>
                    @error('bobot_rupiah')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <input type="hidden" name="bobot_rupiah" value="{{ $task->bobot_rupiah }}">
                @endif
            @endauth
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Task</button>
                <a href="{{ route('project.show', $task->project->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection