<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 14px;
            font-size: 14px;
        }
        /* Versi lebih kompak */
        .compact-summary td {
            padding: 1px 3px !important;
            line-height: 1.2;
            font-size: 12px;
        }

        .compact-summary .label {
            width: 80px;
            padding-right: 5px;
        }

        .compact-summary .value {
            min-width: 80px;
        }
        .label {
            font-weight: bold;
            text-align: right;
            width: 100px; /* Sesuaikan lebar sesuai kebutuhan */
            padding-right: 8px;
        }

        .sep {
            text-align: center;
            width: 10px;
            padding: 0 5px;
        }

        .value {
            text-align: right;
            min-width: 100px; /* Sesuaikan lebar sesuai kebutuhan */

        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
        }
        .logo {
            height:90px;
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
        .divider {
            border-top: 1px solid #000;
            margin: 10px 0;
        }
        .total-section {
            margin-top: 0px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }
        .total-label {
            font-weight: bold;
        }
        .summary {
            width: 40%;
            float: right;
            margin-top: 0px;
        }
        .clearfix {
            clear: both;
        }
        .catatan {
            margin-top: 0px;
        }
    </style>
</head>
<body>
    <div  style="width: 40%; float: right;" margin-top = "30px">
        <div class="invoice-title"># INVOICE</div>
        <div>Nomor&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: INV26022535</div>
        <div>Tanggal&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: 05/03/2025</div>
        <div>Nama Pelanggan : {{ $pesanan->pelanggan->nama_plg ?? '-' }}</div>
    </div>
    <div class="header" >
        <img src="{{ public_path('logo.png') }}" alt="Logo" width="128px">
    </div>

    <div class="divider"></div>

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
                    <td class="sep">{{ $detail->ukuran }}</td>
                    <td>{{ $detail->jumlah.' '.$detail->satuan }} </td>
                    <td class="value">Rp. {{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="value">Rp. {{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    <div style="width: 100%; display: flex; justify-content: flex-end;">
        <table class="summary-table" style="border-collapse: collapse; border: none; font-family: Arial, sans-serif; margin-top: 10px;">
            <tr>
                <td style="text-align: right; padding: 2px 5px; border: none;"><span class="label">Total:</span></td>
                <td style="text-align: right; padding: 2px 5px; border: none; white-space: nowrap; width: 100px;">
                    Rp.&nbsp;<span class="value">{{ number_format($total, 0, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding: 2px 5px; border: none;"><span class="label">Total bayar:</span></td>
                <td style="text-align: right; padding: 2px 5px; border: none; white-space: nowrap;">
                    Rp.&nbsp;<span class="value">{{ number_format($pesanan->totalPembayaran(), 0, ',', '.') }}</span>
                </td>
            </tr>
            <tr>
                <td style="text-align: right; padding: 2px 5px; border: none;"><span class="label">Sisa bon:</span></td>
                <td style="text-align: right; padding: 2px 5px; border: none; white-space: nowrap;">
                    Rp.&nbsp;<span class="value">{{ number_format($total - $pesanan->totalPembayaran(), 0, ',', '.') }}</span>
                </td>
            </tr>
        </table>
    </div>

    <div class="clearfix"></div>

    @if($pesanan->pembayaran->count())
        <div class="catatan">
            <b>Riwayat Pembayaran:</b>
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
        </div>
    @endif
</body>
</html>
