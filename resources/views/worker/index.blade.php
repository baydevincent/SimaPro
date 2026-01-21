@extends('layouts.admin')
@section('title','Master Karyawan')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <h6 class="font-weight-bold">Data Karyawan</h6>
        <div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateWorker">Tambah</button>
        </div>
    </div>
    @include('worker.create')
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Nama</th>
                <th>Jabatan</th>
                <th>No Handphone</th>
                <th>Aksi</th>
            </tr>
            @foreach($workers as $karyawan)
            <tr>
                <td>{{ $karyawan->nama_worker }}</td>
                <td>{{ $karyawan->jabatan }}</td>
                <td>{{ $karyawan->no_hp }}</td>
                <td>
                    <a href="{{ route('worker.edit',$karyawan->id) }}" class="btn btn-warning btn-sm">
                        Edit
                    </a>
                    <button 
                        class="btn btn-danger btn-sm btn-delete-worker"
                        data-id="{{ $karyawan->id }}"
                        data-url="{{ route('worker.destroy', $karyawan->id) }}"
                    >
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection

