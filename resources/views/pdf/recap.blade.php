<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Bulanan {{ $customer->name }} - {{ $monthName }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 12px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            color: #1a1a1a;
            margin: 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
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
        .stats-grid {
            width: 100%;
            margin-bottom: 25px;
            border-spacing: 10px;
            margin-left: -10px;
            margin-right: -10px;
        }
        .stats-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 12px;
            border-radius: 6px;
            vertical-align: top;
        }
        .stats-title {
            font-size: 10px;
            text-transform: uppercase;
            color: #888;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }
        .stats-value {
            font-size: 14px;
            font-weight: bold;
            color: #1a1a1a;
        }
        .stats-subtext {
            font-size: 9px;
            color: #999;
            margin-top: 3px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .items-table th {
            background-color: #e9ecef;
            border-bottom: 2px solid #dee2e6;
            font-weight: bold;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #495057;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            border-radius: 6px;
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
            margin-top: 40px;
            border-top: 1px solid #eee;
            padding-top: 12px;
            text-align: center;
            color: #888;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%">
            <tr>
                <td>
                    <div class="title">HL SALES & RECEIVABLES</div>
                    <div style="color: #666; font-size: 11px; margin-top: 3px;">Laporan Rekap Bulanan Transaksi Pelanggan</div>
                </td>
                <td class="text-right" style="vertical-align: bottom; color: #555; font-size: 11px; font-weight: bold;">
                    Periode: {{ $monthName }}
                </td>
            </tr>
        </table>
    </div>

    <table class="meta-table">
        <tr>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td class="meta-label">Nama Pelanggan</td>
                        <td>: <strong>{{ $customer->name }}</strong></td>
                    </tr>
                    <tr>
                        <td class="meta-label">Cascading Discount LM</td>
                        <td>: {{ empty($customer->discount_lm) ? '-' : implode(' ➔ ', array_map(fn($v) => "$v%", $customer->discount_lm)) }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Cascading Discount BR</td>
                        <td>: {{ empty($customer->discount_br) ? '-' : implode(' ➔ ', array_map(fn($v) => "$v%", $customer->discount_br)) }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 50%">
                <table>
                    <tr>
                        <td class="meta-label">Bonus Eligibility Threshold</td>
                        <td>: Rp {{ number_format($customer->bonus_threshold, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Tanggal Cetak</td>
                        <td>: {{ now()->format('d M Y H:i') }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Monthly Statistics Summary -->
    <table class="stats-grid">
        <tr>
            <td class="stats-card" style="width: 25%">
                <span class="stats-title">Total Piutang</span>
                <div class="stats-value" style="color: #b91c1c;">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</div>
                <div class="stats-subtext">Belum dilunasi</div>
            </td>
            <td class="stats-card" style="width: 25%">
                <span class="stats-title">Sudah Dibayar</span>
                <div class="stats-value" style="color: #047857;">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
                <div class="stats-subtext">Berhasil ditagih</div>
            </td>
            <td class="stats-card" style="width: 25%">
                <span class="stats-title">Total Omzet Lunas</span>
                <div class="stats-value">Rp {{ number_format($omzetLM + $omzetBR, 0, ',', '.') }}</div>
                <div class="stats-subtext">LM: {{ number_format($omzetLM, 0, ',', '.') }} | BR: {{ number_format($omzetBR, 0, ',', '.') }}</div>
            </td>
            <td class="stats-card" style="width: 25%">
                <span class="stats-title">Laba HL Lunas</span>
                <div class="stats-value" style="color: #4f46e5;">Rp {{ number_format($labaLM + $labaBR, 0, ',', '.') }}</div>
                <div class="stats-subtext">LM: {{ number_format($labaLM, 0, ',', '.') }} | BR: {{ number_format($labaBR, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">Nomor Bon</th>
                <th style="width: 15%" class="text-center">Status</th>
                <th style="width: 15%" class="text-center">Jenis</th>
                <th style="width: 20%" class="text-right">Total Tagihan (Piutang)</th>
                <th style="width: 15%" class="text-right">Tgl Lunas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
                <tr>
                    <td>{{ $t->tanggal->format('d M Y') }}</td>
                    <td style="font-family: monospace; font-weight: bold;">{{ $t->nomor_bon }}</td>
                    <td class="text-center">
                        @if($t->status === 'Lunas')
                            <span class="badge badge-lunas">LUNAS</span>
                        @else
                            <span class="badge badge-piutang">PIUTANG</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($t->is_bonus)
                            <span style="color: #4f46e5; font-weight: bold; font-size: 10px;">🎁 BONUS</span>
                        @else
                            <span style="color: #666; font-size: 10px;">SALES</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">Rp {{ number_format($t->total_owed, 2, ',', '.') }}</td>
                    <td class="text-right" style="color: #666;">
                        {{ $t->tanggal_pelunasan ? $t->tanggal_pelunasan->format('d M Y') : '-' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center" style="padding: 20px; color: #888;">
                        Tidak ada transaksi pada periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        HL Sales & Receivables Management System<br>
        Dokumen ini diterbitkan secara resmi oleh sistem manajemen HL.
    </div>
</body>
</html>
