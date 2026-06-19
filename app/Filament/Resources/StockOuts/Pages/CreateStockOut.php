<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateStockOut extends CreateRecord
{
    protected static string $resource = StockOutResource::class;

    protected static ?string $title = 'Tambah Barang Keluar';

    protected static bool $canCreateAnother = false;

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->disabled(function (): bool {
                $items = $this->data['items'] ?? [];

                foreach ($items as $item) {
                    $productId = $item['product_id'] ?? null;
                    $quantity = (int) ($item['quantity'] ?? 0);

                    if (! $productId || $quantity <= 0) {
                        continue;
                    }

                    $product = Product::find($productId);

                    if (! $product || $quantity > $product->stock) {
                        return true;
                    }
                }

                return false;
            });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? null;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));

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

            if ($quantity > $product->stock) {
                Notification::make()
                    ->title('Stok Tidak Mencukupi')
                    ->body("Stok untuk {$product->name} tersedia {$product->stock}, tetapi diminta {$quantity}.")
                    ->danger()
                    ->send();

                $this->halt();
            }
        }

        return $data;
    }
}
