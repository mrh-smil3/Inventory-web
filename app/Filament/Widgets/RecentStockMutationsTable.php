<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\StockMutations\StockMutationResource;
use App\Models\StockMutation;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentStockMutationsTable extends TableWidget
{
    protected static ?int $sort = 7;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Mutasi Stok Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => StockMutation::query()
                ->with('product')
                ->latest('transaction_date')
                ->latest('id'))
            ->columns([
                TextColumn::make('product.name')
                    ->label('Produk')
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'in' => 'Masuk',
                        'out' => 'Keluar',
                        default => $state,
                    }),
                TextColumn::make('quantity')
                    ->label('Jumlah')
                    ->numeric(),
                TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y'),
                TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(40)
                    ->placeholder('—'),
            ])
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5)
            ->headerActions([
                Action::make('viewReport')
                    ->label('Lihat Laporan Mutasi')
                    ->url(StockMutationResource::getUrl('index'))
                    ->icon('heroicon-o-arrow-right'),
            ]);
    }
}
