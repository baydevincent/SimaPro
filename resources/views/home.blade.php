@extends('layouts.admin')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Dashboard') }}</h1>

    @if (session('success'))
    <div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success border-left-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Progress Project</h6>
                </div>
                <div class="card-body">
                    <div style="height:350px">
                        <canvas id="projectBar"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const projectNames = @json($projects->pluck('nama_project'));
    const projectProgress = @json($projects->map->progress());

    new Chart(document.getElementById('projectBar'), {
        type: 'bar',
        data: {
            labels: projectNames,
            datasets: [{
                label: 'Progress (%)',
                data: projectProgress,
                backgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

});
</script>
@endpush

