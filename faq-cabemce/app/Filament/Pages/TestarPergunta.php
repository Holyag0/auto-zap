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
    
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.testar-pergunta';
    
    public ?string $pergunta = '';
    
    public ?string $resposta = null;
    
    public bool $loading = false;

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
        $this->validate();

        try {
            $this->loading = true;
            $this->resposta = null;

            $service = app(N8nService::class);
            $resultado = $service->testarPergunta($this->pergunta);

            if ($resultado['success']) {
                $this->resposta = $resultado['resposta'];
                
                Notification::make()
                    ->title('Resposta recebida com sucesso!')
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title('Erro ao consultar o modelo')
                    ->body($resultado['error'])
                    ->danger()
                    ->send();
                    
                $this->resposta = 'Erro: ' . $resultado['error'];
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
        $this->pergunta = '';
        $this->resposta = null;
        $this->form->fill();
    }
}
