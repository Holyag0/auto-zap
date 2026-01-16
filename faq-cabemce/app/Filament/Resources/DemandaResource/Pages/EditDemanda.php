<?php

namespace App\Filament\Resources\DemandaResource\Pages;

use App\Filament\Resources\DemandaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditDemanda extends EditRecord
{
    protected static string $resource = DemandaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Debug: Ver o que está chegando
        \Log::info('🔍 Dados recebidos antes de salvar:', [
            'arquivos_removidos' => $data['arquivos_removidos'] ?? 'não definido',
            'arquivos_novos' => $data['arquivos_novos'] ?? 'não definido',
            'arquivos_atual_bd' => $this->record->arquivos ?? [],
        ]);
        
        // Recuperar arquivos atuais do banco
        $arquivosAtuais = $this->record->arquivos ?? [];
        
        // Processar arquivos removidos
        $arquivosRemovidos = [];
        if (isset($data['arquivos_removidos'])) {
            if (is_string($data['arquivos_removidos'])) {
                $arquivosRemovidos = json_decode($data['arquivos_removidos'], true) ?? [];
            } elseif (is_array($data['arquivos_removidos'])) {
                $arquivosRemovidos = $data['arquivos_removidos'];
            }
            
            \Log::info('🗑️ Arquivos para remover:', $arquivosRemovidos);
            
            // Remover fisicamente os arquivos
            foreach ($arquivosRemovidos as $arquivo) {
                if (Storage::disk('public')->exists($arquivo)) {
                    Storage::disk('public')->delete($arquivo);
                    \Log::info('✅ Arquivo deletado:', ['arquivo' => $arquivo]);
                }
            }
            
            // Remover da lista de arquivos atuais
            $arquivosAtuais = array_filter($arquivosAtuais, function($arquivo) use ($arquivosRemovidos) {
                return !in_array($arquivo, $arquivosRemovidos);
            });
        }
        
        // Processar novos arquivos
        $arquivosNovos = $data['arquivos_novos'] ?? [];
        if (!empty($arquivosNovos)) {
            \Log::info('➕ Arquivos novos adicionados:', $arquivosNovos);
            $arquivosAtuais = array_merge($arquivosAtuais, $arquivosNovos);
        }
        
        // Reindexar array e limitar a 5 arquivos
        $data['arquivos'] = array_values(array_slice($arquivosAtuais, 0, 5));
        
        \Log::info('💾 Arquivos finais que serão salvos:', $data['arquivos']);
        
        // Remover campos temporários
        unset($data['arquivos_removidos']);
        unset($data['arquivos_novos']);
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index', ['setor' => $this->data['setor_id']]);
    }
}
