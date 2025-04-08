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
    <h2>Slip Gaji Bulanan</h2>
    <p><strong>Bulan:</strong> {{ $tanggal->translatedFormat('F Y') }}</p>

    @foreach($gajiBulanan->groupBy('karyawan_id') as $karyawanId => $data)
        <h3>Nama Karyawan: {{ $data->first()->karyawan->name }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Peran</th>
                    <th>Jumlah</th>
                    <th>Upah</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $gaji)
                    <tr>
                        <td>{{ $gaji->peran }}</td>
                        <td>{{ $gaji->jumlah }}</td>
                        <td>Rp {{ number_format($gaji->upah, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($gaji->total, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <p class="summary"><strong>Total Gaji:</strong> Rp {{ number_format($data->sum('total'), 0, ',', '.') }}</p>
        <hr>
    @endforeach
</body>
</html>
