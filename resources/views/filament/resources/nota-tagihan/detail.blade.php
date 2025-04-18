<div class="p-4 border rounded bg-white dark:bg-gray-900">
    <h2 class="text-lg font-bold">Tagihan</h2>
    <p>Nama Pelanggan: {{ $pelanggan->nama_plg ?? '-' }}</p>

    <table class="w-full mt-4 text-sm">
        <thead>
            <tr>
                <th>Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($details as $item)
                <tr>
                    <td>{{ $item->bahanjadi->nama_bjadi ?? '-' }}</td>
                    <td>{{ $item->jumlah }}</td>
                    <td>@currency($item->harga)</td>
                    <td>@currency($item->jumlah * $item->harga)</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4 border-t pt-2 text-right">
        <p><strong>Total Tagihan:</strong> @currency($total)</p>
        <p><strong>Total Pembayaran:</strong> @currency($totalBayar)</p>
        <p><strong>Sisa Tagihan:</strong> @currency($sisa)</p>
    </div>
</div>
