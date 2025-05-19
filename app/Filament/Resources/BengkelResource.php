<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Bengkel;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TimePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BengkelResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\BengkelResource\RelationManagers;

class BengkelResource extends Resource
{
    protected static ?string $model = Bengkel::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationLabel = 'Bengkel';
    protected static ?string $pluralModelLabel = 'Bengkel';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nama')->required()->maxLength(255),
                TextInput::make('alamat')->required()->maxLength(500),
                Textarea::make('deskripsi')->maxLength(1000),
                TimePicker::make('jam_buka')->required(),
                TimePicker::make('jam_selesai')->required(),
                TextInput::make('lat')->label('Latitude')->numeric()->required(),
                TextInput::make('long')->label('Longitude')->numeric()->required(),
                FileUpload::make('image')->image()->directory('bengkel_images'),
                Select::make('owner_id')
                    ->label('Owner')
                    ->required()
                    ->searchable()
                    ->relationship('owner', 'name', fn($query) => $query->where('role', 'admin_bengkel'))
                    ->visible(fn($livewire) => $livewire instanceof \App\Filament\Resources\BengkelResource\Pages\CreateBengkel),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')->searchable()->sortable(),
                TextColumn::make('alamat')->limit(50),
                TextColumn::make('owner.name')->label('Owner')->sortable(),
                TextColumn::make('jam_buka')->label('Jam Buka')->sortable(),
                TextColumn::make('jam_selesai')->label('Jam Selesai')->sortable(),
                ImageColumn::make('image')->label('Foto')->circular(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBengkels::route('/'),
            'create' => Pages\CreateBengkel::route('/create'),
            'edit' => Pages\EditBengkel::route('/{record}/edit'),
        ];
    }
}
