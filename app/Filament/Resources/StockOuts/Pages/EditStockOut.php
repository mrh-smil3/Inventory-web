<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditStockOut extends EditRecord
{
    protected static string $resource = StockOutResource::class;

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
