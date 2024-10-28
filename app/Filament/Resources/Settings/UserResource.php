<?php

namespace App\Filament\Resources\Settings;

use App\Enums\NavigationGroup;
use App\Filament\Resources\Settings\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = NavigationGroup::Settings->value;

    protected static ?int $navigationSort = 999;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255),
                        Forms\Components\Split::make([
                            Forms\Components\TextInput::make('email')
                                ->required()
                                ->string()
                                ->email()
                                ->maxLength(255),
                            Forms\Components\TextInput::make('password')
                                ->password()
                                ->dehydrated(fn ($state) => ! is_null($state))
                                ->string()
                                ->required(fn (string $context) => $context === 'create'),
                        ]),
                    ]),
                Forms\Components\Section::make(heading: 'Roles & Permissions')
                    ->schema(components: [
                        Forms\Components\CheckboxList::make(name: 'roles')
                            ->relationship(name: 'roles', titleAttribute: 'name')
                            ->columns()
                            ->searchable()
                            ->bulkToggleable(),
                        Forms\Components\CheckboxList::make(name: 'permissions')
                            ->relationship(name: 'permissions', titleAttribute: 'name')
                            ->columns(columns: 3)
                            ->searchable()
                            ->bulkToggleable()
                            ->extraAttributes(attributes: ['style' => 'height: 350px; overflow-y: auto;padding: 10px']),
                    ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make(name: 'id')
                    ->label(label: 'ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make(name: 'name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make(name: 'email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make(name: 'email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make(name: 'roles')
                    ->badge()
                    ->formatStateUsing(fn (Role $state) => $state->name),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
