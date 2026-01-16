<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Filament\Resources\FaqResource\RelationManagers;
use App\Models\Categoria;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    
    protected static ?string $navigationLabel = 'Perguntas e Respostas';
    
    protected static ?string $modelLabel = 'FAQ';
    
    protected static ?string $pluralModelLabel = 'FAQs';
    
    protected static ?string $navigationGroup = 'Gerenciamento FAQ';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da FAQ')
                    ->schema([
                        Forms\Components\Textarea::make('pergunta')
                            ->label('Pergunta')
                            ->required()
                            ->rows(3)
                            ->maxLength(65535)
                            ->placeholder('Digite a pergunta aqui...')
                            ->columnSpanFull(),
                        
                        Forms\Components\Textarea::make('resposta')
                            ->label('Resposta')
                            ->required()
                            ->rows(5)
                            ->maxLength(65535)
                            ->placeholder('Digite a resposta aqui...')
                            ->columnSpanFull(),
                        
                        Forms\Components\Select::make('categoria_id')
                            ->label('Categoria')
                            ->relationship('categoria', 'nome')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('nome')
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\Textarea::make('descricao')
                                    ->rows(2),
                                Forms\Components\ColorPicker::make('cor'),
                                Forms\Components\TextInput::make('ordem')
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->placeholder('Selecione uma categoria')
                            ->helperText('Selecione a categoria ou crie uma nova'),
                        
                        Forms\Components\TextInput::make('categoria')
                            ->label('Categoria (legado)')
                            ->maxLength(100)
                            ->disabled()
                            ->helperText('Campo legado - use o campo acima')
                            ->dehydrated(false)
                            ->visible(fn ($record) => $record && $record->categoria)
                            ->placeholder('Ex: Contatos, Serviços, Endereços, etc.')
                            ->datalist([
                                'Contatos',
                                'Endereços',
                                'Serviços',
                                'Horários',
                                'Informações Gerais',
                            ]),
                        
                        Forms\Components\Toggle::make('ativo')
                            ->label('Ativo')
                            ->default(true)
                            ->helperText('Desative para que essa pergunta não seja usada pelo modelo.')
                            ->inline(false),
                    ])
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('categoria'))
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('pergunta')
                    ->label('Pergunta')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('resposta')
                    ->label('Resposta')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('categoria.nome')
                    ->label('Categoria')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => is_object($record->categoria) ? 'primary' : 'gray')
                    ->icon(fn ($record) => is_object($record->categoria) ? $record->categoria->icone : null)
                    ->tooltip(fn ($record) => is_object($record->categoria) ? $record->categoria->descricao : null),
                
                Tables\Columns\IconColumn::make('ativo')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Criado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizado em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('categoria_id')
                    ->label('Categoria')
                    ->relationship('categoria', 'nome')
                    ->searchable()
                    ->preload(),
                
                TernaryFilter::make('ativo')
                    ->label('Status')
                    ->placeholder('Todos')
                    ->trueLabel('Somente Ativos')
                    ->falseLabel('Somente Inativos'),
            ])
            ->actions([
                Tables\Actions\Action::make('testar')
                    ->label('Testar')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->modalHeading('Testar Pergunta no Modelo')
                    ->modalDescription(fn ($record) => 'Enviar a pergunta "' . \Str::limit($record->pergunta, 50) . '" para o modelo via n8n')
                    ->modalSubmitActionLabel('Enviar para Modelo')
                    ->modalCancelActionLabel('Cancelar')
                    ->requiresConfirmation(false)
                    ->action(function ($record) {
                        $service = app(\App\Services\N8nService::class);
                        $resultado = $service->testarFaq($record);
                        
                        if ($resultado['success']) {
                            \Filament\Notifications\Notification::make()
                                ->title('Resposta do Modelo')
                                ->body($resultado['resposta'])
                                ->success()
                                ->duration(null) // Mantém aberta até fechar manualmente
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Erro ao consultar modelo')
                                ->body($resultado['error'])
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar'),
                Tables\Actions\DeleteAction::make()
                    ->label('Excluir'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Excluir selecionados'),
                ]),
            ])
            ->defaultSort('id', 'desc')
            ->emptyStateHeading('Nenhuma FAQ cadastrada')
            ->emptyStateDescription('Clique no botão abaixo para criar sua primeira pergunta e resposta.')
            ->emptyStateIcon('heroicon-o-question-mark-circle');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}
