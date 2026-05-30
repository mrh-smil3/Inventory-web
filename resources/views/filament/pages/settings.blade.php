<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 32px; display: block;">
            <x-filament::button type="submit">
                Simpan Perubahan
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>