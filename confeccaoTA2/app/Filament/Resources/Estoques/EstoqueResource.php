<?php

namespace App\Filament\Resources\Estoques;

use App\Filament\Resources\Estoques\Pages\CreateEstoque;
use App\Filament\Resources\Estoques\Pages\EditEstoque;
use App\Filament\Resources\Estoques\Pages\ListEstoques;
use App\Filament\Resources\Estoques\Pages\ViewEstoque;
use App\Filament\Resources\Estoques\Schemas\EstoqueInfolist;
use App\Models\Estoque;
use App\Models\Produto;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema; 
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;

class EstoqueResource extends Resource
{
    protected static ?string $model = Estoque::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        
        return $schema->schema([

            Select::make('produto_id')
                ->relationship('produto', 'nome')
                ->searchable()
                ->preload()
                ->required()
                ->label('Selecione o Produto'),

            TextInput::make('quantidade')
                ->numeric()
                ->default(1)
                ->required()
                ->live(onBlur: true),

            Select::make('tipo_movimentacao')
                ->options([
                    'saida' => 'Saída',
                    'entrada' => 'Entrada',
                ])
                ->default('saida') 
                ->required(),

            TextInput::make('observacao')
                ->label('Observação')
                ->maxLength(255)
                ->placeholder('Digite uma observação, se necessário...')
                ->nullable()
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EstoqueInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        
        return $table
            ->columns([

                TextColumn::make('produto.nome')
                    ->label('Produto') 
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantidade')
                    ->label('Quantidade')
                    ->numeric() 
                    ->sortable() 
                    ->alignRight(), 

               TextColumn::make('tipo_movimentacao')
                    ->label('Tipo de Movimentação')
                    ->badge() 
                    ->color(fn (string $state): string => match ($state) {
                        'entrada' => 'success', 
                        'saida' => 'danger',    
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'entrada' => 'heroicon-m-arrow-trending-up', 
                        'saida' => 'heroicon-m-arrow-trending-down', 
                        default => 'heroicon-m-minus',
                    })
                    ->searchable()
                    ->sortable(),

                TextColumn::make('observacao')
                    ->label('Observação')
                    ->wrap()
                    ->toggleable(),
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
            'index' => ListEstoques::route('/'),
            'create' => CreateEstoque::route('/create'),
            'view' => ViewEstoque::route('/{record}'),
            'edit' => EditEstoque::route('/{record}/edit'),
        ];
    }
}