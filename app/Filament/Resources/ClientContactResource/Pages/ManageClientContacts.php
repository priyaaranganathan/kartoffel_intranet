<?php

namespace App\Filament\Resources\ClientContactResource\Pages;

use App\Filament\Resources\ClientContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageClientContacts extends ManageRecords
{
    protected static string $resource = ClientContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
