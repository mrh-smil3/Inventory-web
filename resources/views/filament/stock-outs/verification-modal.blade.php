<div class="stock-out-verification">
    <style>
        .stock-out-verification {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .stock-out-verification .item-row {
            border: 1px solid color-mix(in srgb, currentColor 15%, transparent);
            border-radius: 0.5rem;
            padding: 0.75rem;
        }

        .stock-out-verification .item-name {
            font-size: 0.875rem;
            font-weight: 500;
        }

        .stock-out-verification .item-details {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 0.25rem;
            font-size: 0.875rem;
            opacity: 0.65;
        }

        .stock-out-verification .item-subtotal {
            font-weight: 500;
            opacity: 1;
        }

        .stock-out-verification .empty-state {
            border: 1px solid color-mix(in srgb, currentColor 15%, transparent);
            border-radius: 0.5rem;
            padding: 1rem;
            text-align: center;
            font-size: 0.875rem;
            opacity: 0.65;
        }

        .stock-out-verification .total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid color-mix(in srgb, currentColor 15%, transparent);
            padding-top: 0.75rem;
            font-size: 0.875rem;
            font-weight: 600;
        }
    </style>

    @forelse ($items as $item)
        <div class="item-row">
            <div class="item-name">{{ $item['name'] }}</div>
            <div class="item-details">
                <span>Jumlah Keluar: {{ $item['quantity'] }}</span>
                <span class="item-subtotal">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
            </div>
        </div>
    @empty
        <div class="empty-state">Belum ada barang ditambahkan.</div>
    @endforelse

    <div class="total-row">
        <span>Total</span>
        <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
    </div>
</div>
