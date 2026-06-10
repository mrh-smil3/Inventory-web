<?php

namespace App\Filament\Resources\StockIns\Pages;

use App\Filament\Resources\StockIns\StockInResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStockIn extends EditRecord
{
    protected static string $resource = StockInResource::class;
    protected static ?string $title = 'Edit Barang Masuk';

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? $this->record->product_id;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
