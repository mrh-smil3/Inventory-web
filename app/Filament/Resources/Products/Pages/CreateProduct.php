<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;
    protected static ?string $title = 'Tambah Produk';
    protected static bool $canCreateAnother = false;

    public function hasFormWrapper(): bool
    {
        // Filament renders a native `required` HTML attribute on required fields. With the
        // default native <form wire:submit="create">, the browser's own constraint validation
        // (e.g. "Please fill in this field.") intercepts the click and blocks the request
        // before it ever reaches the server — so the Indonesian ->validationMessages() set
        // on ProductForm's fields never get a chance to render. Removing the form wrapper
        // means the button calls create() directly over Livewire instead of a native submit,
        // so the browser can't intervene and our own validation messages show correctly.
        return false;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->disabled(function (): bool {
                $sellingPrice = (float) ($this->data['selling_price'] ?? 0);
                $purchasePrice = (float) ($this->data['purchase_price'] ?? 0);

                return $sellingPrice < $purchasePrice;
            });
    }

    protected function mutateFormDataBeforeCreate(array $data): array
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
}
