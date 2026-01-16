<?php

namespace App\Filament\Resources\DemandaResource\Pages;

use App\Filament\Resources\DemandaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateDemanda extends CreateRecord
{
    protected static string $resource = DemandaResource::class;

    /**
     * Pré-preenche o formulário com dados da URL
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Se vier setor na URL, já preenche
        if (request()->has('setor') && empty($data['setor_id'])) {
            $data['setor_id'] = request()->get('setor');
        }

        return $data;
    }

    /**
     * Redireciona após criar, voltando para o setor se veio de lá
     */
    protected function getRedirectUrl(): string
    {
        if (request()->has('setor')) {
            return DemandaResource::getUrl('index', ['setor' => request()->get('setor')]);
        }

        return $this->getResource()::getUrl('index');
    }
}
