<?php

namespace App\Filament\Resources\EmailsPedidos;

use App\Filament\Resources\EmailsPedidos\Pages\ListEmailsPedidos;
use App\Filament\Resources\EmailsPedidos\Pages\ViewEmailPedido;
use App\Models\EmailPedido;
use App\Support\FilamentAccess;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class EmailPedidoResource extends Resource
{
    protected static ?string $model = EmailPedido::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Vendas';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Caixa de E-mail';

    protected static ?string $modelLabel = 'E-mail de Pedido';

    protected static ?string $pluralModelLabel = 'Caixa de E-mail';

    protected static ?string $recordTitleAttribute = 'assunto';

    protected static ?string $slug = 'caixa-de-email';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return FilamentAccess::canAny('caixa_email.visualizar');
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Dados do envio')
                    ->schema([
                        TextEntry::make('pedido_id')
                            ->label('Pedido')
                            ->formatStateUsing(fn (int $state): string => "#{$state}"),
                        TextEntry::make('cliente.nome')
                            ->label('Cliente')
                            ->placeholder('Cliente removido'),
                        TextEntry::make('email_destinatario')
                            ->label('Destinatário')
                            ->placeholder('Não informado'),
                        TextEntry::make('assunto')
                            ->label('Assunto'),
                        TextEntry::make('status_envio')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn (?string $state): string => EmailPedido::statusLabel($state))
                            ->color(fn (?string $state): string => EmailPedido::statusColor($state)),
                        TextEntry::make('enviado_em')
                            ->label('Enviado em')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('Não enviado'),
                    ])
                    ->columns(3),

                Section::make('Resumo registrado')
                    ->schema([
                        TextEntry::make('conteudo_resumo')
                            ->label('Conteúdo')
                            ->placeholder('Sem resumo registrado.')
                            ->columnSpanFull(),
                        TextEntry::make('erro_envio')
                            ->label('Erro')
                            ->placeholder('Nenhum erro registrado.')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('pedido_id')
                    ->label('Pedido')
                    ->formatStateUsing(fn (int $state): string => "#{$state}")
                    ->sortable(),
                TextColumn::make('cliente.nome')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email_destinatario')
                    ->label('Destinatário')
                    ->searchable()
                    ->placeholder('Sem e-mail'),
                TextColumn::make('assunto')
                    ->label('Assunto')
                    ->limit(45)
                    ->searchable(),
                TextColumn::make('status_envio')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => EmailPedido::statusLabel($state))
                    ->color(fn (?string $state): string => EmailPedido::statusColor($state))
                    ->sortable(),
                TextColumn::make('enviado_em')
                    ->label('Data de envio')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Não enviado')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Registrado em')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status_envio')
                    ->label('Status')
                    ->options(EmailPedido::statusOptions()),

                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nome')
                    ->searchable()
                    ->preload(),

                Filter::make('enviado_em')
                    ->label('Data de envio')
                    ->schema([
                        DatePicker::make('sent_from')->label('De'),
                        DatePicker::make('sent_until')->label('Até'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['sent_from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('enviado_em', '>=', $date))
                            ->when($data['sent_until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('enviado_em', '<=', $date));
                    }),
            ])
            ->recordActions([
                ViewAction::make()->label('Detalhes'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmailsPedidos::route('/'),
            'view' => ViewEmailPedido::route('/{record}'),
        ];
    }
}
