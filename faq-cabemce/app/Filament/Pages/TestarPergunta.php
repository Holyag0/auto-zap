<?php

namespace App\Filament\Pages;

use App\Services\N8nService;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;

class TestarPergunta extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    
    protected static ?string $navigationLabel = 'Testar Perguntas';
    
    protected static ?string $title = 'Testar Perguntas no Modelo';
    
    protected static ?string $navigationGroup = 'Gerenciamento FAQ';
    
    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.testar-pergunta';
    
    public ?array $data = [];
    
    public ?string $resposta = null;
    
    public bool $loading = false;
    
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('pergunta')
                    ->label('Digite sua pergunta')
                    ->placeholder('Ex: Qual o horário de funcionamento?')
                    ->required()
                    ->rows(4)
                    ->maxLength(1000)
                    ->helperText('Digite uma pergunta para testar como o modelo irá responder.')
                    ->autofocus(),
            ])
            ->statePath('data');
    }

    public function testar(): void
    {
        // Valida o formulário
        $data = $this->form->getState();
        
        if (empty($data['pergunta'])) {
            Notification::make()
                ->title('Erro')
                ->body('Por favor, digite uma pergunta.')
                ->danger()
                ->send();
            return;
        }

        try {
            $this->loading = true;
            $this->resposta = null;

            $service = app(N8nService::class);
            $resultado = $service->testarPergunta($data['pergunta']);

            if ($resultado['success']) {
                $this->resposta = $resultado['resposta'];
                
                Notification::make()
                    ->title('Resposta recebida com sucesso!')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro ao consultar o modelo')
                    ->body($resultado['resposta'] ?? 'Erro desconhecido')
                    ->danger()
                    ->send();
                    
                $this->resposta = 'Erro: ' . ($resultado['resposta'] ?? 'Erro desconhecido');
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Erro ao processar requisição')
                ->body($e->getMessage())
                ->danger()
                ->send();
                
            $this->resposta = 'Erro: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function limpar(): void
    {
        $this->data = [];
        $this->resposta = null;
        $this->form->fill();
        
        Notification::make()
            ->title('Formulário limpo')
            ->success()
            ->send();
    }
}
