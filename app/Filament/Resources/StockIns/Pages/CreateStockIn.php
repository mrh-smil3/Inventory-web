<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockIn extends CreateRecord
{
    protected static string $resource = StockInResource::class;
    protected static ?string $title = 'Tambah Barang Masuk';
    protected static bool $canCreateAnother = false;

    public function hasFormWrapper(): bool
    {
        // See App\Filament\Resources\Products\Pages\CreateProduct::hasFormWrapper() —
        // without this, the browser's native `required` constraint validation blocks
        // submission before the Indonesian ->validationMessages() on StockInForm's
        // fields ever get a chance to render.
        return false;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? null;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
        $data['total_price'] = $items->sum(fn (array $item): float => (float) ($item['subtotal'] ?? 0));

        return $data;
    }
    

    protected function afterCreate(): void
    {
        $totalPrice = $this->record->items()->sum('subtotal');
        $this->record->update(['total_price' => $totalPrice]);
    }
}
