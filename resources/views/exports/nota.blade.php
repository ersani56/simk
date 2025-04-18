<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 12px;
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
            height: 80px;
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
            @endphp
            @foreach($pesanan->pesananDetails as $detail)
                @php
                    $subtotal = $detail->jumlah * $detail->harga;
                    $total += $subtotal;
                    $totalQty += $detail->jumlah;
                @endphp
                <tr>
                    <td>{{ $detail->bahanjadi->nama_bjadi ?? '-' }}</td>
                    <td class="sep">{{ $detail->ukuran }}</td>
                    <td>{{ $detail->jumlah }} pcs</td>
                    <td class="value">{{ number_format($detail->harga, 0, ',', '.') }}</td>
                    <td class="value">{{ number_format($subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="summary-compact" style="float: right; text-align: right;">
        <div style="margin-bottom: 2px;">
            <span class="label">Total&nbsp;&nbsp;&nbsp;:</span>
            <span class="value">&nbsp;{{ number_format($total, 0, ',', '.') }}&nbsp;</span>
        </div>
        <div style="margin-bottom: 2px;">
            <span class="label">Total bayar&nbsp;&nbsp;:</span>
            <span class="value">&nbsp;{{ number_format($pesanan->totalPembayaran(), 0, ',', '.') }}&nbsp;</span>
        </div>
        <div>
            <span class="label">Sisa bon&nbsp;&nbsp;:</span>
            <span class="value">{{ number_format($total - $pesanan->totalPembayaran(), 0, ',', '.') }}&nbsp;</span>
        </div>
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
                            <td>{{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</body>
</html>
