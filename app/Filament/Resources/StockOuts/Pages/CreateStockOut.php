<?php

namespace App\Filament\Resources\StockOuts\Pages;

use App\Filament\Resources\StockOuts\StockOutResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Cancel;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;

class CreateStockOut extends CreateRecord
{
    protected static string $resource = StockOutResource::class;

    protected static ?string $title = 'Tambah Barang Keluar';

    protected static bool $canCreateAnother = false;

    public function hasFormWrapper(): bool
    {
        // Without this, the page renders a native <form wire:submit="create">, and
        // pressing Enter inside any field submits it directly — bypassing the
        // requiresConfirmation() modal set up below entirely.
        return false;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            // Mirrors the quantity field's ->rules() closure in StockOutForm — if either
            // check would fail validation there, keep the button disabled here so the
            // verification modal never opens; the field's own afterStateUpdated() already
            // surfaces a notification for both cases as soon as the user blurs the input.
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

                    if (($product->stock - $quantity) < $product->min_stock) {
                        return true;
                    }
                }

                return false;
            })
            // Default create action renders as a native `type="submit"` button (via ->submit()),
            // which bypasses the Livewire action-mount flow entirely — requiresConfirmation()
            // and modalContent() would silently never fire. Passing a string to ->action() has
            // the same problem: Filament renders that as a raw wire:click="create" and still
            // skips mountAction() (see Action::getLivewireClickHandler()). Only a Closure here
            // routes the click through mountAction(), which is what actually opens the modal.
            ->submit(null)
            // Runs when the button is clicked, before the modal opens. Validating the main
            // form here (required fields, the quantity ->rules() closure, etc.) means an
            // invalid form throws before the modal ever shows. Without this, the modal has
            // no schema of its own, so nothing validated the real form until "Ya, Simpan"
            // was clicked and create() ran, letting the popup appear over an invalid form.
            //
            // Filament's own mountAction() pushes this action onto $this->mountedActions
            // (a persisted Livewire property) *before* calling this closure. If we let a
            // ValidationException escape here, it isn't caught anywhere inside mountAction()
            // (which only catches Halt/Cancel), so unmountAction() never runs and the action
            // stays stranded in $this->mountedActions across requests. The next unrelated
            // Livewire update (e.g. deleting the invalid repeater item) then rehydrates with
            // that stale mounted action, re-evaluates it against the now-valid data, and pops
            // the modal open on its own. Throwing Cancel instead routes through Filament's own
            // catch (Cancel $exception) branch in mountAction(), which calls unmountAction()
            // for us and cleans up $this->mountedActions immediately, in this same request.
            ->mountUsing(function (): void {
                try {
                    $this->form->getState();
                } catch (ValidationException $exception) {
                    Notification::make()
                        ->title('Lengkapi Data Terlebih Dahulu')
                        ->body(collect($exception->errors())->flatten()->first() ?? 'Periksa kembali data pada formulir sebelum menyimpan.')
                        ->danger()
                        ->send();

                    throw new Cancel;
                }
            })
            ->action(fn () => $this->create())
            ->requiresConfirmation()
            ->modalHeading('Verifikasi Barang Keluar')
            ->modalDescription('Pastikan daftar barang yang keluar di bawah ini sudah benar sebelum disimpan.')
            ->modalSubmitActionLabel('Ya, Simpan')
            ->modalCancelActionLabel('Batal, Edit Kembali')
            // Rendered via a plain Blade view (resources/views/filament/stock-outs/verification-modal.blade.php)
            // instead of a schema/infolist, since RepeatableEntry inside an action's schema
            // wasn't picking up the live form state reliably.
            ->modalContent(fn (): View => view('filament.stock-outs.verification-modal', [
                'items' => collect($this->data['items'] ?? [])
                    ->map(fn (array $item): array => [
                        'name' => Product::find($item['product_id'] ?? null)?->name ?? '-',
                        'quantity' => (int) ($item['quantity'] ?? 0),
                        'subtotal' => (float) ($item['subtotal'] ?? 0),
                    ]),
                'total' => collect($this->data['items'] ?? [])
                    ->sum(fn (array $item): float => (float) ($item['subtotal'] ?? 0)),
            ]));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $items = collect($data['items'] ?? []);

        $data['product_id'] = $items->first()['product_id'] ?? null;
        $data['quantity'] = $items->sum(fn (array $item): int => (int) ($item['quantity'] ?? 0));
        $data['total_price'] = $items->sum(fn (array $item): float => (float) ($item['subtotal'] ?? 0));

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

    protected function afterCreate(): void
    {
        $totalPrice = $this->record->items()->sum('subtotal');
        $this->record->update(['total_price' => $totalPrice]);
    }
}
