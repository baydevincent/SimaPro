<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Harian - {{ $project->nama_project }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
            font-weight: normal;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 5px;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 150px;
            font-weight: bold;
        }
        
        .section {
            margin-bottom: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        
        .content-box {
            background-color: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            text-align: justify;
        }
        
        .photos-grid {
            width: 100%;
            border-collapse: collapse;
        }
        
        .photos-grid td {
            width: 33.33%;
            padding: 5px;
            vertical-align: top;
            text-align: center;
        }
        
        .photo-container {
            border: 1px solid #ddd;
            padding: 5px;
            margin-bottom: 10px;
        }
        
        .photo-container img {
            max-width: 100%;
            height: 150px;
            object-fit: cover;
        }
        
        .photo-caption {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 40px;
            width: 100%;
        }
        
        .signature-box {
            display: inline-block;
            width: 45%;
            text-align: center;
            vertical-align: top;
        }
        
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #333;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>LAPORAN HARIAN PROJECT</h1>
        <h2>{{ $project->nama_project }}</h2>
    </div>

    <!-- Informasi Umum -->
    <table class="info-table">
        <tr>
            <td class="label">Tanggal</td>
            <td>: {{ \Carbon\Carbon::parse($report->tanggal)->format('d F Y') }}</td>
            <td class="label">Dibuat Oleh</td>
            <td>: {{ $report->creator ? $report->creator->name : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Cuaca</td>
            <td>: {{ $report->cuaca ?? '-' }}</td>
            <td class="label">Jumlah Pekerja</td>
            <td>: {{ $report->jumlah_pekerja }} Orang</td>
        </tr>
    </table>

    <!-- Uraian Kegiatan -->
    <div class="section">
        <div class="section-title">URAIAN KEGIATAN</div>
        <div class="content-box">
            {!! nl2br(e($report->uraian_kegiatan)) !!}
        </div>
    </div>

    <!-- Catatan Tambahan -->
    @if($report->catatan)
    <div class="section">
        <div class="section-title">CATATAN TAMBAHAN</div>
        <div class="content-box" style="background-color: #fff3cd;">
            {!! nl2br(e($report->catatan)) !!}
        </div>
    </div>
    @endif

    <!-- Dokumentasi Foto -->
    @if($report->images->count() > 0)
    <div class="section">
        <div class="section-title">DOKUMENTASI FOTO ({{ $report->images->count() }} Foto)</div>
        <table class="photos-grid">
            @foreach($report->images->chunk(3) as $imageChunk)
            <tr>
                @foreach($imageChunk as $image)
                <td>
                    <div class="photo-container">
                        @if(file_exists(public_path('storage/' . $image->image_path)))
                        <img src="{{ public_path('storage/' . $image->image_path) }}" alt="Foto">
                        @else
                        <div style="height: 150px; background-color: #eee; display: flex; align-items: center; justify-content: center; color: #999;">
                            Foto tidak tersedia
                        </div>
                        @endif
                        @if($image->caption)
                        <div class="photo-caption">{{ $image->caption }}</div>
                        @endif
                    </div>
                </td>
                @endforeach
                @if($imageChunk->count() < 3)
                    @for($i = 0; $i < (3 - $imageChunk->count()); $i++)
                    <td></td>
                    @endfor
                @endif
            </tr>
            @endforeach
        </table>
    </div>
    @endif

    <!-- Footer / Signature -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Dibuat Oleh,</div>
            <div class="signature-line">
                {{ $report->creator ? $report->creator->name : '-' }}
            </div>
        </div>
        <div class="signature-box">
            <div>Mengetahui,</div>
            <div class="signature-line">
                ( Project Manager )
            </div>
        </div>
    </div>

    <!-- Footer Info -->
    <div class="footer">
        Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB
    </div>
</body>
</html>
