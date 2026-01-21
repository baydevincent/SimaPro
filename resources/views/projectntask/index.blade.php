@extends('layouts.admin')
@section('title','Master Project')


@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <h6 class="font-weight-bold">Data Project</h6>
        <div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateProject">Tambah</button>
        </div>
    </div>
    @include('projectntask.pcreate')
    <div class="card-body">
        <table class="table table-bordered">
            <tr>
                <th>Project</th>
                <th>Nilai</th>
                <th>Progress</th>
                <th>Aksi</th>
            </tr>
            @foreach($projects as $p)
            <tr>
                <td>{{ $p->nama_project }}</td>
                <td>
                    @auth
                        @if(Auth::user()->hasRole('administrator'))
                            Rp {{ number_format($p->nilai_project) }}
                        @else
                            <span class="text-muted">***</span>
                        @endif
                    @else
                        <span class="text-muted">***</span>
                    @endauth
                </td>
                <td>
                    <div class="progress">
                        <div class="progress-bar bg-success"
                            style="width: {{ $p->progress() }}%">
                            {{ $p->progress() }}%
                        </div>
                    </div>
                </td>
                <td>
                    @if($p->id)
                        <a href="{{ route('project.show',['project' => $p->id]) }}" class="btn btn-info btn-sm">
                            Detail
                        </a>
                    @else
                        <span class="btn btn-info btn-sm disabled">Detail</span>
                    @endif
                    <button 
                        class="btn btn-warning btn-sm btn-delete-project"
                        data-id="{{ $p->id }}"
                        data-url="{{ route('project.destroy', $p->id) }}"
                    >
                        <i class="fas fa-edit"></i>
                    </button>
                    <button 
                        class="btn btn-danger btn-sm btn-delete-project"
                        data-id="{{ $p->id }}"
                        data-url="{{ route('project.destroy', $p->id) }}"
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

