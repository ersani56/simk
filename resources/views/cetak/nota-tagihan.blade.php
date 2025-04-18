<h2>Nota Tagihan</h2>
<p>Nama Pelanggan: {{ $pesanan->pelanggan->nama_plg }}</p>
<p>No Faktur: {{ $pesanan->no_faktur }}</p>
<p>Tanggal: {{ $pesanan->tanggal }}</p>

<table border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Harga</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($details as $item)
        <tr>
            <td>{{ $item->bahanjadi->nama_bjadi ?? '-' }}</td>
            <td>{{ $item->jumlah }}</td>
            <td>@currency($item->harga)</td>
            <td>@currency($item->jumlah * $item->harga)</td>
        </tr>
        @endforeach
    </tbody>
</table>

<p><strong>Total Tagihan:</strong> @currency($total)</p>
<p><strong>Total Pembayaran:</strong> @currency($totalBayar)</p>
<p><strong>Sisa Tagihan:</strong> @currency($sisa)</p>
