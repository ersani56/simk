<!DOCTYPE html>
<html>
<head><title>Slip Gaji</title></head>
<body>
    <h2>Slip Gaji Karyawan</h2>
    <p>Nama: {{ $gaji->karyawan->name }}</p>
    <p>Peran: {{ ucfirst($gaji->peran) }}</p>
    <p>Jumlah: {{ $gaji->jumlah }}</p>
    <p>Harga Satuan: Rp{{ number_format($gaji->harga_satuan) }}</p>
    <p>Total: <strong>Rp{{ number_format($gaji->total) }}</strong></p>
    <p>Tanggal: {{ $gaji->created_at->format('d-m-Y') }}</p>
</body>
</html>
