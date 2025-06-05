<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji Bulanan</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        h2, h3 { margin-bottom: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
        .summary { margin-top: 20px; }
    </style>
</head>
<body>
    @php
    $kasbon = App\Models\Kasbon::whereIn('user_id', $gajiBulanan->pluck('karyawan_id'))->get()->groupBy('user_id');
    @endphp
    @foreach($gajiBulanan->groupBy('karyawan_id') as $karyawanId => $data)
        @php $index = $loop->index @endphp
    <div style="{{ $index > 0 ? 'page-break-before: always;' : '' }} margin-bottom: 5px;">
        <h2>Slip Gaji Bulanan</h2>
    </div>
    <div style="margin-bottom: 5px" >
        <strong>Bulan&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong> {{ $tanggal->translatedFormat('F Y') }}
    </div>
    <div style="margin-bottom: 5px" >
        <strong>Nama Karyawan :</strong> {{ $data->first()->karyawan->name }}
        <hr>
    </div>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 5px; border: none;">
            <tr>
                <td style="width: 33.33%; border: none;"><strong>Total Gaji:</strong> Rp {{ number_format($data->sum('total'), 0, ',', '.') }}</td>
                <td style="width: 33.33%; border: none;"><strong>Kasbon:</strong> Rp {{ number_format($kasbon->get($data->first()->karyawan_id, collect())->sum('jumlah'), 0, ',', '.') }}</td>
                <td style="width: 33.33%; border: none;"><strong>Gaji Bersih:</strong> Rp {{ number_format($data->sum('total') - $kasbon->get($data->first()->karyawan_id, collect())->sum('jumlah'), 0, ',', '.') }}</td>
            </tr>
        </table>
        <table>
            <thead>
                <tr>
                    <th>Peran</th>
                    <th>Nama produk</th>
                    <th>Size</th>
                    <th>Qty</th>
                    <th>Upah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $gaji)
                    <tr>
                        <td>{{ $gaji->peran }}</td>
                        <td>{{ $gaji->pesananDetail?->produk?->nama_bjadi ?? '-' }}</td>
                        <td>{{ $gaji->pesananDetail->ukuran ?? '-' }}</td>
                        <td>{{ $gaji->jumlah }}</td>
                        <td>Rp {{ number_format($gaji->upah, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($gaji->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @endforeach
</body>
</html>
