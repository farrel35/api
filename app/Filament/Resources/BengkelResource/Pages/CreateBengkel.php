<?php

namespace App\Filament\Resources\BengkelResource\Pages;

use App\Models\Bengkel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BengkelResource;
use Illuminate\Validation\ValidationException;

class CreateBengkel extends CreateRecord
{
    protected static string $resource = BengkelResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $ownerId = $data['owner_id'];

        if (Bengkel::where('owner_id', $ownerId)->exists()) {
            abort(403, 'User sudah memiliki bengkel.');
        }
        return $data;
    }

}
