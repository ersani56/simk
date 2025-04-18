<h2>Nota Tagihan: {{ $pesanan->no_faktur }}</h2>
<p>Nama Pelanggan: {{ $pesanan->pelanggan->nama }}</p>
<p>Tanggal: {{ $pesanan->tanggal }}</p>

<table border="1" cellpadding="6" cellspacing="0">
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th>Ukuran</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($details as $item)
            <tr>
                <td>{{ $item->bahanjadi->nama_bjadi ?? '-' }}</td>
                <td>{{ $item->ukuran }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ number_format($item->harga, 0, ',', '.') }}</td>
                <td>{{ number_format($item->jumlah * $item->harga, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Total: Rp {{ number_format($details->sum(fn($d) => $d->jumlah * $d->harga), 0, ',', '.') }}</strong></p>
