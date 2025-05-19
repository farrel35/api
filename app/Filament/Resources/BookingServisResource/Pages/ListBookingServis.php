<?php

namespace App\Filament\Resources\BookingServisResource\Pages;

use App\Filament\Resources\BookingServisResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookingServis extends ListRecords
{
    protected static string $resource = BookingServisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
