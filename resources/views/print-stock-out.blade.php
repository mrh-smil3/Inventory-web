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
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <strong>Pratinjau Cetak Barang Keluar</strong>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Sekarang</button>
            <button onclick="window.close()" class="btn">Tutup Halaman</button>
        </div>
    </div>

    <div class="header">
        <div>
            <h1>Sistem Inventory Web</h1>
            <p class="muted">Dokumen transaksi barang keluar</p>
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
        <div class="value">{{ $stockOut->transaction_date ? \Illuminate\Support\Carbon::parse($stockOut->transaction_date)->format('d/m/Y') : '-' }}</div>
        <div class="label">Total Jumlah</div>
        <div class="value">{{ number_format($stockOut->items->sum('quantity')) }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th style="width: 140px;">SKU</th>
                <th>Nama Barang</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 90px;">Satuan</th>
                <th class="text-right" style="width: 120px;">Jumlah Keluar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($stockOut->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->product?->sku ?? '-' }}</td>
                    <td>{{ $item->product?->name ?? '-' }}</td>
                    <td>{{ $item->product?->category?->name ?? '-' }}</td>
                    <td>{{ $item->product?->unit?->name ?? '-' }}</td>
                    <td class="text-right">{{ number_format($item->quantity) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #94a3b8; padding: 28px;">Tidak ada item barang keluar.</td>
                </tr>
            @endforelse
            <tr class="total-row">
                <td colspan="5" class="text-right">Total</td>
                <td class="text-right">{{ number_format($stockOut->items->sum('quantity')) }}</td>
            </tr>
        </tbody>
    </table>

    @if($stockOut->note)
        <div class="note">
            <strong>Catatan</strong>
            <p class="muted">{{ $stockOut->note }}</p>
        </div>
    @endif

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
