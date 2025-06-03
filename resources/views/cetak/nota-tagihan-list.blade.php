<!DOCTYPE html>
<html>
<head>
    <title>Daftar Tagihan</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        h3, h4 { margin-bottom: 5px; }
        ul { list-style: none; padding: 0; }
    </style>
</head>
<body>
    <h3>Daftar Tagihan Pelanggan</h3>

    <table>
        <thead>
            <tr>
                <th>No Faktur</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Total Tagihan</th>
                <th>Total Bayar</th>
                <th>Sisa</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pesanans as $p)
                <tr>
                    <td>{{ $p->pesanan_id }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d M Y') }}</td>
                    <td>{{ $p->pelanggan->nama_plg ?? '-' }}</td>
                    <td>{{ number_format($p->total_tagihan ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format($p->total_bayar ?? 0, 0, ',', '.') }}</td>
                    <td>{{ number_format(($p->total_tagihan - $p->total_bayar) ?? 0, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h4>Ringkasan:</h4>
    <ul>
        <li>Total Tagihan: Rp {{ number_format($totalTagihan, 0, ',', '.') }}</li>
        <li>Total Dibayar: Rp {{ number_format($totalBayar, 0, ',', '.') }}</li>
        <li>Sisa Tagihan: Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</li>
    </ul>
</body>
</html>
