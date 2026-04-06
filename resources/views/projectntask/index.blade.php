@extends('layouts.admin')
@section('title','Master Project')

@section('main-content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between">
        <h6 class="font-weight-bold">Data Project</h6>
        <div class="d-flex">
            <div class="mr-2">
                <label for="sort_order" class="col-form-label">Urutkan:</label>
            </div>
            <div class="mr-2">
                <select id="sort_order" class="form-control" onchange="changeSortOrder(this.value)">
                    <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>A-Z (Ascending)</option>
                    <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Z-A (Descending)</option>
                </select>
            </div>
            <div>
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalCreateProject">Tambah</button>
            </div>
        </div>
    </div>
    @include('projectntask.pcreate')
    
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
    
    <div class="card-body">
        <table class="table table-bordered table-responsive">
            <tr>
                <th width="25%">Project</th>
                <th width="15%">Nilai</th>
                <th width="10%">Start</th>
                <th width="10%">Deadline</th>
                <th width="15%">Progress</th>
                <th width="15%">Aksi</th>
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
                <td>{{ \Carbon\Carbon::parse($p->tanggal_mulai)->format('d M Y') }}</td>
                <td>{{ $p->tanggal_selesai ? \Carbon\Carbon::parse($p->tanggal_selesai)->format('d M Y') : '-' }}</td>
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
                            <i class="fas fa-eye"></i>
                        </a>
                    @else
                        <span class="btn btn-info btn-sm disabled">Detail</span>
                    @endif
                    <a href="{{ route('project.edit', $p->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
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
        
        <!-- Pagination links -->
        <div class="d-flex justify-content-center">
            {{ $projects->appends(['order' => request()->get('order')])->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function changeSortOrder(order) {
    window.location.href = '?order=' + order;
}
</script>

</new_string>