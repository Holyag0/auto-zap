<?php

namespace App\Filament\Pages;

use App\Models\Setor;
use Filament\Pages\Page;

class GerenciarQRCodes extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR Codes';
    protected static ?string $title = 'Gerenciar QR Codes dos Setores';
    protected static ?string $navigationGroup = 'Atendimento';
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.gerenciar-qr-codes';

    public function getSetoresProperty()
    {
        return Setor::with('configuracao')
            ->where('ativo', true)
            ->get()
            ->map(function ($setor) {
                // Garante que tem configuração
                if (!$setor->configuracao) {
                    $setor->configuracao()->create([
                        'codigo_acesso' => \Illuminate\Support\Str::random(8),
                        'prefixo' => $setor->sigla,
                    ]);
                    $setor->refresh();
                }
                return $setor;
            });
    }

    public function gerarNovoCodigo($setorId)
    {
        $setor = Setor::findOrFail($setorId);
        
        if ($setor->configuracao) {
            $novoCodigo = \Illuminate\Support\Str::random(8);
            $setor->configuracao->update(['codigo_acesso' => $novoCodigo]);
            
            \Filament\Notifications\Notification::make()
                ->title('Código atualizado!')
                ->body("Novo código para {$setor->nome}: {$novoCodigo}")
                ->success()
                ->send();
        }
    }

    public function baixarQRCode($setorId)
    {
        $setor = Setor::findOrFail($setorId);
        $url = route('senha.create', $setor->id);
        
        // Retorna para download
        return redirect()->to("https://api.qrserver.com/v1/create-qr-code/?size=1000x1000&data=" . urlencode($url) . "&download=1&filename=qrcode-{$setor->sigla}.png");
    }
}
