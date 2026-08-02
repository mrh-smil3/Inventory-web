<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barang Keluar - {{ $stockOut->invoice_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 30px;
            background: #fff;
            font-size: 13px;
        }

        .watermark-logo {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        opacity: 0.2; /* Atur transparansi di sini (0.0 - 1.0) */
        z-index: 10;
        pointer-events: none; /* Agar watermark tidak bisa diklik/di-select */
        }
        .watermark-logo img {
            width: 300px; /* Sesuaikan ukuran logo */
        }

        .toolbar {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            padding: 8px 14px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
            background: #fff;
            color: #334155;
            cursor: pointer;
            text-decoration: none;
            font-weight: 500;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            color: #fff;
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            border-bottom: 3px double #d1d5db;
            padding-bottom: 18px;
            margin-bottom: 24px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .header-left .logo img {
            width: 48px;
            height: 48px;
            object-fit: contain;
        }

        h1, h2, p {
            margin: 0;
        }

        h1 {
            font-size: 22px;
            text-transform: uppercase;
            color: #111827;
        }

        h2 {
            font-size: 20px;
            color: #dc2626;
            text-align: right;
        }

        .muted {
            color: #64748b;
            margin-top: 4px;
        }

        .meta {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 8px 12px;
            margin-bottom: 24px;
            max-width: 520px;
        }

        .meta .label {
            color: #64748b;
        }

        .meta .value {
            font-weight: 600;
            color: #0f172a;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            color: #334155;
            text-align: left;
            padding: 10px;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 11px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .total-row td {
            font-weight: 700;
            color: #111827;
            border-top: 2px solid #cbd5e1;
        }

        .note {
            margin-top: 24px;
            padding-top: 14px;
            border-top: 1px solid #e5e7eb;
        }

        .footer {
            margin-top: 46px;
            display: flex;
            justify-content: space-between;
            color: #94a3b8;
            font-size: 11px;
        }

        .signature-section {
            margin-top: 50px;
            display: flex;
            justify-content: right;
            gap: 40px;
        }

        .signature-block {
            width: 200px;
            text-align: center;
        }

        .signature-block .signature-line {
            border-top: 1px solid #1f2937;
            margin-top: 80px;
            padding-top: 8px;
            font-weight: 600;
            color: #0f172a;
        }

        .signature-block .signature-name {
            font-size: 12px;
            color: #475569;
            margin-top: 4px;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            th {
                background: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .watermark-logo {
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                opacity: 0.15;
                z-index: 10;
                pointer-events: none;
            }
        }
    </style>
</head>
<body>
    <div class="watermark-logo">
        <img src="{{ asset('logo.png') }}" alt="Watermark Logo">
    </div>
    <div class="toolbar no-print">
        <strong>Pratinjau Cetak Barang Keluar</strong>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Sekarang</button>
            <button onclick="window.close()" class="btn">Tutup Halaman</button>
        </div>
    </div>

    <div class="header">
        <div>
            <div class="header-left">
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
                </div>
                <h1>{{ config('app.name') }}</h1>
            </div>
            <p class="muted" style="margin-top: 6px;">DS. Karanganyar RT.07/RW.01</p>
        </div>
        <div>
            <h2>BARANG KELUAR</h2>
            <p class="muted">Dicetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        </div>
    </div>

    <div class="meta">
        <div class="label">No. Invoice</div>
        <div class="value">{{ $stockOut->invoice_number }}</div>
        <div class="label">Tanggal Transaksi</div>
        <div class="value">{{ $stockOut->transaction_date ? \Illuminate\Support\Carbon::parse($stockOut->transaction_date)->format('d/m/Y H:i') : '-' }}</div>
        <div class="label">Total Jumlah</div>
        <div class="value">{{ number_format($stockOut->items->sum('quantity')) }}</div>
        <div class="label">Total Harga</div>
        <div class="value">Rp {{ number_format($stockOut->total_price ?? $stockOut->items->sum('subtotal'), 0, ',', '.') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 120px;">SKU</th>
                <th>Nama Barang</th>
                <th style="width: 90px;">Satuan</th>
                <th class="text-right" style="width: 90px;">Harga Satuan</th>
                <th class="text-right" style="width: 70px;">Jumlah</th>
                <th class="text-right" style="width: 120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockOut->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product?->sku ?? '-' }}</td>
                    <td>{{ $item->product?->name ?? '-' }}</td>
                    <td>{{ $item->product?->unit?->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($item->unit_price ?? 0, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                    <td class="text-right">Rp {{ number_format($item->subtotal ?? 0, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 28px;">Tidak ada item barang keluar.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">Total</td>
                <td class="text-right">{{ number_format($stockOut->items->sum('quantity')) }}</td>
                <td class="text-right">Rp {{ number_format($stockOut->total_price ?? $stockOut->items->sum('subtotal'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($stockOut->note)
        <div class="note">
            <strong>Catatan</strong>
            <p class="muted">{{ $stockOut->note }}</p>
        </div>
    @endif

    <div class="signature-section">
        <!-- <div class="signature-block">
            <div class="signature-line">Mengetahui,</div>
            <div class="signature-name">{{ config('app.name') }}</div>
        </div> -->
        <div class="signature-block">
            <div class="signature-line">Dikeluarkan Oleh,</div>
            <div class="signature-name">{{ config('app.name') }}</div>
        </div>
    </div>

    <div class="footer">
        <div>Dicetak oleh sistem secara otomatis.</div>
        <div>{{ $stockOut->invoice_number }}</div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
