<?php

namespace App\Filament\Resources\BookingServisResource\Pages;

use App\Filament\Resources\BookingServisResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBookingServis extends EditRecord
{
    protected static string $resource = BookingServisResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
