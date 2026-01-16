<?php

namespace App\Filament\Resources\TipoDemandaResource\Pages;

use App\Filament\Resources\TipoDemandaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoDemandas extends ListRecords
{
    protected static string $resource = TipoDemandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
