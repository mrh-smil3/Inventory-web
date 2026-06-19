<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStockOut extends EditRecord
{
    protected static string $resource = StockOutResource::class;

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->disabled(function (): bool {
                $items = $this->data['items'] ?? [];
                $existingItems = $this->record?->items()->get()->keyBy('id') ?? collect();

                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $quantity = (int) ($item['quantity'] ?? 0);

                    if (! $productId || $quantity <= 0) {
                        continue;
                    }

                    $product = Product::find($productId);

                    if (! $product) {
                        continue;
                    }

                    $availableStock = $product->stock;

                    if (isset($item['id']) && ($existingItem = $existingItems->get($item['id']))) {
                        $availableStock += $existingItem->quantity;
                    }

                    if ($quantity > $availableStock) {
                        return true;
                    }
                }

                return false;
            });
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? $this->record->product_id;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));

        $existingItems = $this->record->items()->get()->keyBy('id');

        foreach ($items as $item) {
            $productId = $item['product_id'] ?? null;
            $quantity = (int) ($item['quantity'] ?? 0);

            if (! $productId || $quantity <= 0) {
                continue;
            }

            $product = Product::find($productId);

            if (! $product) {
                continue;
            }

            $availableStock = $product->stock;

            if (isset($item['id']) && ($existingItem = $existingItems->get($item['id']))) {
                $availableStock += $existingItem->quantity;
            }

            if ($quantity > $availableStock) {
                Notification::make()
                    ->title('Stok Tidak Mencukupi')
                    ->body("Stok untuk {$product->name} tersedia {$availableStock}, tetapi diminta {$quantity}.")
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

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
