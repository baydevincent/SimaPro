@extends('layouts.admin')
@section('title','Edit Project')

@section('main-content')
<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Edit Project</h6>
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

        <form action="{{ route('project.update', $project->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="nama_project">Nama Project</label>
                <input type="text" name="nama_project" class="form-control @error('nama_project') is-invalid @enderror" 
                       id="nama_project" value="{{ old('nama_project', $project->nama_project) }}" required>
                @error('nama_project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="nilai_project">Nilai Project (Rp)</label>
                <input type="number" name="nilai_project" class="form-control @error('nilai_project') is-invalid @enderror" 
                       id="nilai_project" value="{{ old('nilai_project', $project->nilai_project) }}" required>
                @error('nilai_project')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                       id="tanggal_mulai" value="{{ old('tanggal_mulai', $project->tanggal_mulai) }}" required>
                @error('tanggal_mulai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                       id="tanggal_selesai" value="{{ old('tanggal_selesai', $project->tanggal_selesai) }}">
                @error('tanggal_selesai')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update Project</button>
                <a href="{{ route('project') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection