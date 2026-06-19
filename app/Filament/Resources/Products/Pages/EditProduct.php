<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $title = 'Edit Produk';

    protected function getSaveFormAction(): Action
    {
        return parent::getSaveFormAction()
            ->disabled(function (): bool {
                $sellingPrice = (float) ($this->data['selling_price'] ?? 0);
                $purchasePrice = (float) ($this->data['purchase_price'] ?? 0);

                return $sellingPrice < $purchasePrice;
            });
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $sellingPrice = (float) ($data['selling_price'] ?? 0);
        $purchasePrice = (float) ($data['purchase_price'] ?? 0);

        if ($sellingPrice < $purchasePrice) {
            Notification::make()
                ->title('Harga Jual harus lebih besar dari Harga Beli')
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
