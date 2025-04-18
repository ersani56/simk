<h2>Nota Tagihan: {{ $pesanan->no_faktur }}</h2>
<p>Pelanggan: {{ $pesanan->pelanggan->nama_plg }}</p>
<p>Tanggal: {{ $pesanan->tanggal }}</p>

<table>
    <thead>
        <tr>
            <th>Nama Barang</th>
            <th>Ukuran</th>
            <th>Harga</th>
            <th>Jumlah</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pesanan->pesananDetail as $item)
            <tr>
                <td>{{ $item->bahanjadi->nama_bjadi }}</td>
                <td>{{ $item->ukuran }}</td>
                <td>{{ number_format($item->harga) }}</td>
                <td>{{ $item->jumlah }}</td>
                <td>{{ number_format($item->harga * $item->jumlah) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p>Total: Rp{{ number_format($totalTagihan) }}</p>
<p>DP: Rp{{ number_format($totalDP) }}</p>
<p><strong>Sisa: Rp{{ number_format($sisaTagihan) }}</strong></p>
