<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Slip Gaji</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        h2 { text-align: center; }
        table { width: 100%; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Slip Gaji Karyawan</h2>
    <table>
        <tr><td>Nama</td><td>: {{ $gaji->karyawan->name }}</td></tr>
        <tr><td>Peran</td><td>: {{ ucfirst($gaji->peran) }}</td></tr>
        <tr><td>Jumlah</td><td>: {{ $gaji->jumlah }}</td></tr>
        <tr><td>Harga Satuan</td><td>: Rp{{ number_format($gaji->harga_satuan) }}</td></tr>
        <tr><td>Total</td><td>: <strong>Rp{{ number_format($gaji->total) }}</strong></td></tr>
        <tr><td>Tanggal</td><td>: {{ $gaji->created_at->format('d-m-Y') }}</td></tr>
    </table>
</body>
</html>
