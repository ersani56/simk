<!DOCTYPE html>
<html>
<head>
    <title>Nota Tagihan List</title>
    <style>
        /* Tambahkan style yang sesuai */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 14px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            line-height: 1;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    @foreach($notaTagihan as $pesanan)
        <div style="page-break-after: always;">
            <h2>Nota Tagihan</h2>
            <p>Nama Pelanggan: {{ $pesanan->pelanggan->nama_plg }}</p>
            <p>No Faktur: {{ $pesanan->no_faktur }}</p>
            <p>Tanggal: {{ $pesanan->tanggal }}</p>

            <table>
                <thead>
                    <tr>
                        <th>Nama Barang</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $total = 0;
                    $totalQty = 0;
                    $grouped = $pesanan->pesananDetails
                        ->filter(fn($detail) => $detail->setelan === null || $detail->satuan === 'stel')
                        ->groupBy(fn($d) => $d->bahanjadi->nama_bjadi ?? '-');
                @endphp

                @foreach($grouped as $namaBarang => $items)
                    @foreach($items as $index => $detail)
                        @php
                            $subtotal = $detail->jumlah * $detail->harga;
                            $total += $subtotal;
                            $totalQty += $detail->jumlah;
                        @endphp
                        <tr>
                            @if($index === 0)
                            <td rowspan="{{ $items->count() }}" style="vertical-align: top;">{{ $namaBarang }}</td>
                            @endif
                            <td>{{ $detail->ukuran }}</td>
                            <td>{{ $detail->jumlah.' '.$detail->satuan }} </td>
                            <td>Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                            <td>Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>

            <p>Total: Rp. {{ number_format($total, 0, ',', '.') }}</p>
            <p>Total Bayar: Rp. {{ number_format($pesanan->totalPembayaran(), 0, ',', '.') }}</p>
            <p>Sisa Bon: Rp. {{ number_format($total - $pesanan->totalPembayaran(), 0, ',', '.') }}</p>

            @if($pesanan->pembayaran->count())
                <h2>Riwayat Pembayaran</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nominal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pesanan->pembayaran as $p)
                            <tr>
                                <td>{{ $p->tanggal_bayar }}</td>
                                <td>Rp. {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endforeach
</body>
</html>
