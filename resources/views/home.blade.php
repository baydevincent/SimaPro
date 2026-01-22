@extends('layouts.admin')

@section('main-content')

    <!-- Page Heading -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('Dashboard') }}</h1>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('home') }}" class="mt-3 mt-md-0">
            <div class="form-row align-items-center">
                <div class="col-auto">
                    <label class="sr-only" for="tahun">Tahun</label>
                    <select name="tahun" id="tahun" class="form-control">
                        <option value="">Semua Tahun</option>
                        @foreach($availableYears as $year)
                            <option value="{{ $year }}" {{ $tahun == $year ? 'selected' : '' }}>
                                {{ $year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        <option value="">Semua Bulan</option>
                        <option value="01" {{ $bulan == '01' ? 'selected' : '' }}>Januari</option>
                        <option value="02" {{ $bulan == '02' ? 'selected' : '' }}>Februari</option>
                        <option value="03" {{ $bulan == '03' ? 'selected' : '' }}>Maret</option>
                        <option value="04" {{ $bulan == '04' ? 'selected' : '' }}>April</option>
                        <option value="05" {{ $bulan == '05' ? 'selected' : '' }}>Mei</option>
                        <option value="06" {{ $bulan == '06' ? 'selected' : '' }}>Juni</option>
                        <option value="07" {{ $bulan == '07' ? 'selected' : '' }}>Juli</option>
                        <option value="08" {{ $bulan == '08' ? 'selected' : '' }}>Agustus</option>
                        <option value="09" {{ $bulan == '09' ? 'selected' : '' }}>September</option>
                        <option value="10" {{ $bulan == '10' ? 'selected' : '' }}>Oktober</option>
                        <option value="11" {{ $bulan == '11' ? 'selected' : '' }}>November</option>
                        <option value="12" {{ $bulan == '12' ? 'selected' : '' }}>Desember</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </form>
    </div>

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

    // Inisialisasi chart
    const ctx = document.getElementById('projectBar');
    const projectChart = new Chart(ctx, {
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

    // Tambahkan event listener untuk filter
    document.getElementById('tahun').addEventListener('change', function() {
        document.querySelector('form[method="GET"]').submit();
    });

    document.getElementById('bulan').addEventListener('change', function() {
        document.querySelector('form[method="GET"]').submit();
    });
});
</script>
@endpush

