<?php

namespace App\Filament\Resources\DemandaResource\Pages;

use App\Filament\Resources\DemandaResource;
use App\Models\Setor;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListDemandas extends ListRecords
{
    protected static string $resource = DemandaResource::class;
    
    protected static string $view = 'filament.resources.demanda-resource.pages.list-demandas';

    protected function getHeaderActions(): array
    {
        // Só mostra o botão quando NÃO estiver visualizando por setor
        // Quando estiver por setor, o botão está na view customizada
        if (request()->has('setor')) {
            return [];
        }

        return [
            Actions\CreateAction::make()
                ->label('Nova Demanda')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * Pega os setores para exibir nos cards
     */
    public function getSetores()
    {
        return Setor::withCount(['demandas as total_demandas'])
            ->withCount(['demandas as em_analise' => function ($query) {
                $query->where('situacao', 'em_analise');
            }])
            ->withCount(['demandas as em_andamento' => function ($query) {
                $query->where('situacao', 'em_andamento');
            }])
            ->withCount(['demandas as concluidas' => function ($query) {
                $query->where('situacao', 'concluida');
            }])
            ->where('ativo', true)
            ->orderBy('nome')
            ->get();
    }

    /**
     * Verifica se tem filtro de setor ativo
     */
    public function hasSetorFilter(): bool
    {
        $tableFilters = $this->tableFilters;
        return isset($tableFilters['setor_id']['value']) && !empty($tableFilters['setor_id']['value']);
    }

    /**
     * Modifica a query para filtrar por setor se necessário
     */
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        
        // Se houver filtro de setor na URL, aplica
        if (request()->has('setor')) {
            $query->where('setor_id', request('setor'));
        }
        
        return $query;
    }
}
