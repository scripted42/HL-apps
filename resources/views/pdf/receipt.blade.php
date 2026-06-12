<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bon {{ $transaction->nomor_bon }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 25px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 15px;
        }
        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 25px;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
        }
        .meta-label {
            font-weight: bold;
            color: #555;
            width: 120px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: bold;
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            color: #495057;
        }
        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .summary-container {
            width: 300px;
            float: right;
            margin-top: 15px;
        }
        .summary-table {
            width: 100%;
            border-collapse: collapse;
        }
        .summary-table td {
            padding: 6px 10px;
        }
        .summary-label {
            color: #666;
        }
        .summary-value {
            font-weight: bold;
            text-align: right;
        }
        .total-row td {
            border-top: 2px solid #ddd;
            font-size: 15px;
            padding-top: 10px;
        }
        .total-value {
            color: #4f46e5;
        }
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 10px;
            font-weight: bold;
            border-radius: 10px;
            text-transform: uppercase;
        }
        .badge-lunas {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-piutang {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 15px;
            text-align: center;
            color: #888;
            font-size: 11px;
            clear: both;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="title">HL SALES & RECEIVABLES</div>
                    <div style="color: #666; font-size: 11px; margin-top: 3px;">Faktur Penjualan (Bon) Resmi</div>
                </td>
                <td class="text-right">
                    @if ($transaction->status === 'Lunas')
                        <span class="badge badge-lunas">LUNAS</span>
                    @else
                        <span class="badge badge-piutang">PIUTANG</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td class="meta-label">Nomor Bon</td>
                        <td>: <strong>{{ $transaction->nomor_bon }}</strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal</td>
                        <td>: {{ $transaction->tanggal->format('d M Y') }}</td>
                    </tr>
                    @if($transaction->status === 'Lunas' && $transaction->tanggal_pelunasan)
                    <tr>
                        <td class="meta-label">Tgl Pelunasan</td>
                        <td>: {{ $transaction->tanggal_pelunasan->format('d M Y') }}</td>
                    </tr>
                    @endif
                </table>
            </td>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td class="meta-label">Pelanggan</td>
                        <td>: <strong>{{ $transaction->customer->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Jenis Transaksi</td>
                        <td>: {{ $transaction->is_bonus ? 'Transaksi Bonus (Free Items)' : 'Penjualan Reguler' }}</td>
                    </tr>
                    @if($transaction->deskripsi)
                    <tr>
                        <td class="meta-label">Catatan</td>
                        <td>: {{ $transaction->deskripsi }}</td>
                    </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 45%">Nama Produk</th>
                <th style="width: 10%" class="text-center">Tipe</th>
                <th style="width: 10%" class="text-center">Qty</th>
                <th style="width: 15%" class="text-right">Harga Diskon/Unit</th>
                <th style="width: 15%" class="text-right">Subtotal Omzet</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center"><span style="font-weight: 500;">{{ $item->product_type }}</span></td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->discounted_unit_price, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->line_omzet, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary-container">
        <table class="summary-table">
            <tr>
                <td class="summary-label">Total Omzet</td>
                <td class="summary-value">Rp {{ number_format($transaction->omzet, 2, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="summary-label">Ongkos Kirim</td>
                <td class="summary-value">Rp {{ number_format($transaction->ongkir, 2, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td class="summary-label" style="font-weight: bold;">
                    {{ $transaction->is_bonus ? 'Total Biaya' : 'Total Piutang' }}
                </td>
                <td class="summary-value total-value">Rp {{ number_format($transaction->total_owed, 2, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Terima Kasih atas Kemitraan Anda dengan HL.<br>
        Dokumen ini dibuat otomatis secara sah tanpa tanda tangan fisik.
    </div>
</body>
</html>
