<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok Barang - {{ now()->format('d/m/Y') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            margin: 0;
            padding: 30px;
            background-color: #fff;
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
        
        .header {
            margin-bottom: 30px;
            border-bottom: 3px double #ddd;
            padding-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
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
        
        .company-details h1 {
            margin: 0 0 5px 0;
            font-size: 24px;
            color: #111;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .company-details p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        
        .report-title {
            text-align: right;
        }
        
        .report-title h2 {
            margin: 0 0 5px 0;
            font-size: 20px;
            color: #4f46e5;
            font-weight: 600;
        }
        
        .report-title p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 13px;
        }
        
        th {
            background-color: #f8fafc;
            color: #1e293b;
            font-weight: 600;
            text-align: left;
            padding: 12px 10px;
            border-bottom: 2px solid #e2e8f0;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 12px 10px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        
        tr:nth-child(even) {
            background-color: #f8fafc;
        }
        
        .text-right {
            text-align: right;
        }
        
        .badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 11px;
            font-weight: 600;
            border-radius: 4px;
            background-color: #f1f5f9;
            color: #475569;
        }
        
        .footer {
            margin-top: 50px;
            font-size: 11px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }
        
        .toolbar {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        
        .btn-primary {
            background-color: #4f46e5;
            color: #fff;
        }
        
        .btn-primary:hover {
            background-color: #4338ca;
        }
        
        .btn-secondary {
            background-color: #fff;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        
        .btn-secondary:hover {
            background-color: #f8fafc;
            border-color: #94a3b8;
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
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            tr:nth-child(even) {
                background-color: #f8fafc !important;
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
        <div>
            <span style="font-size: 14px; font-weight: 500; color: #1e293b;">Pratinjau Cetak Laporan Stok</span>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Sekarang</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup Halaman</button>
        </div>
    </div>

    <div class="header">
        <div class="company-details">
            <div class="header-left">
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}">
                </div>
                <h1>{{ config('app.name') }}</h1>
            </div>
            <p class="muted" style="margin-top: 6px;">DS. Karanganyar RT.07/RW.01</p>
        </div>
        <div class="report-title">
            <h2>LAPORAN STOK BARANG</h2>
            <p>Tanggal Cetak: {{ now()->translatedFormat('d F Y H:i') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th style="width: 120px;">SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th style="width: 80px;">Satuan</th>
                <th class="text-right" style="width: 100px;">Stok Masuk</th>
                <th class="text-right" style="width: 100px;">Stok Keluar</th>
                <th class="text-right" style="width: 100px;">Sisa Stok</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $index => $product)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td><span class="badge">{{ $product->sku }}</span></td>
                    <td style="font-weight: 500; color: #0f172a;">{{ $product->name }}</td>
                    <td>{{ $product->category?->name ?? '-' }}</td>
                    <td>{{ $product->unit?->name ?? '-' }}</td>
                    <td class="text-right" style="font-weight: 500; color: #16a34a;">{{ number_format($product->total_stock_in ?? 0) }}</td>
                    <td class="text-right" style="font-weight: 500; color: #dc2626;">{{ number_format($product->total_stock_out ?? 0) }}</td>
                    <td class="text-right" style="font-weight: 600; color: #1e293b;">{{ number_format($product->stock) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 30px;">Tidak ada data stok barang yang tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

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
        <div>Halaman 1 dari 1</div>
    </div>

    <script>
        // Automatically trigger print dialog on page load (excluding cancel states)
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
