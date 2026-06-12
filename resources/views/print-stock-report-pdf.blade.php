<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Stok Barang</title>

    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #000;
            padding: 6px;
        }

        table th {
            background: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>

<h2>Laporan Stok Barang</h2>

<p>
    Tanggal Cetak:
    {{ $generatedAt->format('d/m/Y H:i') }}
</p>

<table>
    <thead>
        <tr>
            <th>SKU</th>
            <th>Nama Produk</th>
            <th>Kategori</th>
            <th>Satuan</th>
            <th>Stok Masuk</th>
            <th>Stok Keluar</th>
            <th>Sisa Stok</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->sku }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category?->name ?? '-' }}</td>
                <td>{{ $product->unit?->name ?? '-' }}</td>
                <td class="text-right">
                    {{ $product->total_stock_in ?? 0 }}
                </td>
                <td class="text-right">
                    {{ $product->total_stock_out ?? 0 }}
                </td>
                <td class="text-right">
                    {{ $product->stock }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>