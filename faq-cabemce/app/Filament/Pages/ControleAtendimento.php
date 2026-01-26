<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Setor;

class ControleAtendimento extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Controle de Atendimento';

    protected static ?string $title = 'Controle de Atendimento';

    protected static ?string $navigationGroup = 'Atendimento';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.controle-atendimento';

    public function getSetores()
    {
        return Setor::ativos()
            ->with('configuracao')
            ->get()
            ->filter(function ($setor) {
                return $setor->configuracao && $setor->configuracao->ativo;
            });
    }
}
