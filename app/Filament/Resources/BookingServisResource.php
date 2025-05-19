<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\BookingServis;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BookingServisResource\Pages;
use App\Filament\Resources\BookingServisResource\RelationManagers;

class BookingServisResource extends Resource
{
    protected static ?string $model = BookingServis::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('nama')->searchable()->sortable(),
            TextColumn::make('no_hp')->label('No. HP'),
            TextColumn::make('nama_kendaraan')->label('Kendaraan'),
            TextColumn::make('plat')->label('Plat'),
            TextColumn::make('status')
                ->badge()
                ->formatStateUsing(fn(int $state): string => match ($state) {
                    0 => 'Pending',
                    1 => 'Diservis',
                    2 => 'Menunggu Diambil',
                    3 => 'Selesai',
                    default => 'Unknown',
                })
                ->color(fn(int $state): string => match ($state) {
                    0 => 'warning',   // kuning untuk pending
                    1 => 'gray',   // biru untuk diservis
                    2 => 'info', // abu-abu untuk menunggu diambil
                    3 => 'success',   // hijau untuk selesai
                    default => 'secondary',
                })
                ->label('Status'),


            TextColumn::make('user.name')->label('User')->sortable(),
            TextColumn::make('bengkel.nama')->label('Bengkel')->sortable(),
        ])
            ->actions([
                Tables\Actions\ViewAction::make(),  // enables view page
                // Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('nama')->label('Nama Pelanggan'),
                TextEntry::make('no_hp')->label('No. HP'),
                TextEntry::make('nama_kendaraan')->label('Nama Kendaraan'),
                TextEntry::make('plat')->label('Plat Kendaraan'),
                TextEntry::make('keluhan')->label('Keluhan'),
                TextEntry::make('tgl_booking')->label('Tanggal Booking')->date(),
                TextEntry::make('jam_booking')->label('Jam Booking'),
                TextEntry::make('tgl_ambil')->label('Tanggal Ambil')->date(),
                TextEntry::make('jam_ambil')->label('Jam Ambil'),

                TextEntry::make('user.name')->label('User'),
                TextEntry::make('bengkel.nama')->label('Bengkel'),

                RepeatableEntry::make('jenis_layanan')
                    ->label('Jenis Layanan')
                    ->schema([
                        TextEntry::make('layanan')->label('Nama Layanan'),
                        TextEntry::make('harga_layanan')->label('Harga'),
                    ])->columnSpanFull(),
                RepeatableEntry::make('detail_servis')
                    ->label('Detail Servis')
                    ->schema([
                        TextEntry::make('sparepart')->label('Nama Sparepart'),
                        TextEntry::make('harga_sparepart')->label('Harga'),
                    ])->columnSpanFull(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookingServis::route('/'),
            // 'create' => Pages\CreateBookingServis::route('/create'),
            // 'edit' => Pages\EditBookingServis::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
