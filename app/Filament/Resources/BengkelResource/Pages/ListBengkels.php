<?php

namespace App\Filament\Resources\BengkelResource\Pages;

use App\Filament\Resources\BengkelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBengkels extends ListRecords
{
    protected static string $resource = BengkelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
