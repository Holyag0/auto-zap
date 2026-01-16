<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DemandaResource\Pages;
use App\Filament\Resources\DemandaResource\RelationManagers;
use App\Models\Demanda;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DemandaResource extends Resource
{
    protected static ?string $model = Demanda::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    
    protected static ?string $navigationLabel = 'Demandas';
    
    protected static ?string $modelLabel = 'Demanda';
    
    protected static ?string $pluralModelLabel = 'Demandas';
    
    protected static ?string $navigationGroup = 'Sistema de Demandas';
    
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informações da Demanda')
                    ->schema([
                        Forms\Components\TextInput::make('protocolo')
                            ->label('Protocolo')
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Gerado automaticamente ao salvar')
                            ->visible(fn ($record) => $record !== null),
                        
                        Forms\Components\Select::make('setor_id')
                            ->label('Setor')
                            ->relationship('setor', 'nome')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                // Limpa o responsável quando o setor muda
                                $set('responsavel_id', null);
                            })
                            ->default(fn () => request()->get('setor'))
                            ->helperText('Setor responsável pela demanda'),
                        
                        Forms\Components\Select::make('tipo_demanda_id')
                            ->label('Tipo de Demanda')
                            ->relationship('tipoDemanda', 'nome', fn ($query) => $query->where('ativo', true))
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
                            ])
                            ->helperText('Tipo/categoria da solicitação'),
                        
                        Forms\Components\Select::make('responsavel_id')
                            ->label('Responsável')
                            ->relationship(
                                name: 'responsavel',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn ($query, Forms\Get $get) => $query
                                    ->when(
                                        $get('setor_id'),
                                        fn ($query, $setorId) => $query->where('setor_id', $setorId)
                                    )
                                    ->with('setor')
                            )
                            ->getOptionLabelFromRecordUsing(fn ($record) => 
                                $record->setor 
                                    ? "{$record->name} ({$record->setor->sigla})"
                                    : $record->name
                            )
                            ->searchable(['name', 'email'])
                            ->preload()
                            ->nullable()
                            ->disabled(fn (Forms\Get $get) => !$get('setor_id'))
                            ->helperText(fn (Forms\Get $get) => 
                                $get('setor_id') 
                                    ? 'Selecione um usuário do setor escolhido (opcional)'
                                    : 'Selecione um setor primeiro'
                            ),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Descrição')
                    ->schema([
                        Forms\Components\Textarea::make('descricao')
                            ->label('Descrição da Demanda')
                            ->required()
                            ->rows(5)
                            ->placeholder('Descreva detalhadamente sua solicitação...')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Anexos')
                    ->schema([
                        Forms\Components\FileUpload::make('arquivos')
                            ->label('Imagens/Prints')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(5120)
                            ->disk('public')
                            ->imageEditor()
                            ->imageEditorAspectRatios([
                                null,
                                '16:9',
                                '4:3',
                                '1:1',
                            ])
                            ->directory('demandas/anexos')
                            ->visibility('public')
                            ->downloadable()
                            ->openable()
                            ->reorderable()
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                            ->helperText('Envie até 5 imagens (prints, fotos) para ajudar no entendimento da demanda. Máximo 5MB por arquivo.')
                            ->columnSpanFull()
                            ->visible(fn ($operation) => $operation === 'create'),
                    ])
                    ->visible(fn ($operation) => $operation === 'create'),
                
                Forms\Components\Section::make('Gerenciar Anexos')
                    ->description('Visualize, remova ou adicione novas imagens')
                    ->schema([
                        Forms\Components\View::make('filament.components.demanda-images-edit')
                            ->viewData(fn ($record) => [
                                'arquivos' => $record?->arquivos ?? [],
                                'recordId' => $record?->id,
                            ])
                            ->columnSpanFull(),
                        
                        Forms\Components\Hidden::make('arquivos_removidos')
                            ->default('[]')
                            ->dehydrated()
                            ->live(),
                        
                        Forms\Components\FileUpload::make('arquivos_novos')
                            ->label('Adicionar Novas Imagens')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->maxSize(5120)
                            ->disk('public')
                            ->directory('demandas/anexos')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/jpg', 'image/gif', 'image/webp'])
                            ->helperText('Adicione até 5 novas imagens.')
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($operation, $record) => $operation === 'edit' && $record !== null)
                    ->collapsible(),
                
                Forms\Components\Section::make('Imagens Anexadas')
                    ->schema([
                        Forms\Components\View::make('filament.components.demanda-images')
                            ->viewData(fn ($record) => [
                                'arquivos' => $record?->arquivos ?? [],
                            ])
                            ->columnSpanFull(),
                    ])
                    ->visible(fn ($record, $operation) => $operation === 'view' && $record && !empty($record->arquivos)),

                Forms\Components\Section::make('Informações Adicionais')
                    ->schema([
                        Forms\Components\Select::make('situacao')
                            ->label('Situação')
                            ->options([
                                'em_analise' => 'Em Análise',
                                'em_andamento' => 'Em Andamento',
                                'concluida' => 'Concluída',
                                'cancelada' => 'Cancelada',
                            ])
                            ->default('em_analise')
                            ->required()
                            ->visible(fn ($record) => $record !== null),
                        
                        Forms\Components\DateTimePicker::make('data_conclusao')
                            ->label('Data de Conclusão')
                            ->visible(fn ($record) => $record !== null && in_array($record->situacao, ['concluida', 'cancelada']))
                            ->helperText('Data em que a demanda foi finalizada'),
                        
                        Forms\Components\Textarea::make('observacoes')
                            ->label('Observações')
                            ->rows(3)
                            ->placeholder('Observações adicionais sobre a demanda...')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->visible(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('protocolo')
                    ->label('Protocolo')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                
                Tables\Columns\TextColumn::make('setor.nome')
                    ->label('Setor')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('tipoDemanda.nome')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn ($record) => 'primary'),
                
                Tables\Columns\TextColumn::make('situacao')
                    ->label('Situação')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'em_analise' => 'warning',
                        'em_andamento' => 'info',
                        'concluida' => 'success',
                        'cancelada' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'em_analise' => 'Em Análise',
                        'em_andamento' => 'Em Andamento',
                        'concluida' => 'Concluída',
                        'cancelada' => 'Cancelada',
                        default => $state,
                    })
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('responsavel.name')
                    ->label('Responsável')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Não atribuído')
                    ->icon('heroicon-o-user'),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Solicitada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('data_conclusao')
                    ->label('Concluída em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Atualizada em')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListDemandas::route('/'),
            'create' => Pages\CreateDemanda::route('/create'),
            'view' => Pages\ViewDemanda::route('/{record}'),
            'edit' => Pages\EditDemanda::route('/{record}/edit'),
        ];
    }
}
