<?php

namespace App\Filament\Technicien\Resources;

use App\Filament\Technicien\Resources\TicketResource\Pages;
use App\Models\Ticket;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\Select::make('equipement_id')
                            ->relationship('equipement', 'designation')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->label('Equipment'),

                        Forms\Components\Select::make('type_ticket')
                            ->options([
                                'correctif' => 'Corrective Maintenance',
                                'preventif' => 'Preventive Maintenance',
                                'installation' => 'Installation',
                                'formation' => 'Training',
                                'autre' => 'Other',
                            ])
                            ->required()
                            ->default('correctif'),

                        Forms\Components\Select::make('statut')
                            ->options([
                                'nouveau' => 'New',
                                'attribue' => 'Assigned',
                                'en_cours' => 'In Progress',
                                'en_attente' => 'Pending',
                                'cloture' => 'Closed',
                            ])
                            ->required()
                            ->default('nouveau')
                            ->live(),

                        Forms\Components\Select::make('priorite')
                            ->options([
                                'critique' => 'Critical - Urgent',
                                'haute' => 'High',
                                'moyenne' => 'Medium',
                                'basse' => 'Low',
                            ])
                            ->required()
                            ->default('moyenne'),
                    ]),

                Forms\Components\Section::make('Problem Details')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('chemin_image')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->directory('tickets')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Assignment')
                    ->schema([
                        Forms\Components\Hidden::make('user_createur_id')
                            ->default(fn () => auth()->id())
                            ->required(),

                        Forms\Components\Select::make('user_assignee_id')
                            ->label('Assigned Technician')
                            ->options(
                                User::whereNotIn('role', ['admin', 'responsable'])
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn (Forms\Get $get): bool =>
                                in_array($get('statut'), ['attribue', 'en_cours', 'en_attente', 'cloture'])),

                        Forms\Components\DateTimePicker::make('date_attribution')
                            ->visible(fn (Forms\Get $get): bool =>
                                in_array($get('statut'), ['attribue', 'en_cours', 'en_attente', 'cloture']))
                            ->default(now()),

                        Forms\Components\DateTimePicker::make('date_cloture')
                            ->visible(fn (Forms\Get $get): bool =>
                                $get('statut') === 'cloture')
                            ->default(now()),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('equipement.designation')
                    ->sortable()
                    ->searchable()
                    ->label('Equipment')
                    ->url(fn ($record) => route('filament.technicien.resources.tickets.show', $record->id)),

                Tables\Columns\TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable(),

                Tables\Columns\TextColumn::make('statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'nouveau' => 'gray',
                        'attribue' => 'info',
                        'en_cours' => 'warning',
                        'cloture' => 'success',
                        default => 'danger',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('priorite')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critique' => 'danger',
                        'haute' => 'warning',
                        'moyenne' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('type_ticket')
                    ->label('Ticket Type')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->options([
                        'nouveau' => 'New',
                        'attribue' => 'Assigned',
                        'en_cours' => 'In Progress',
                        'cloture' => 'Closed',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
            'show' => Pages\ShowTicket::route('/{record}'),
        ];
    }
}
