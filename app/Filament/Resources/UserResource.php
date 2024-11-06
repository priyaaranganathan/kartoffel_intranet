<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\RecordStatus;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name'),
                Forms\Components\TextInput::make('last_name'),
                Forms\Components\TextInput::make('phonenumber')
                    ->tel()
                    ->numeric(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required(fn ($record) => is_null($record)) 
                    ->password()
                    ->hidden(fn ($record) => $record !== null),
                Forms\Components\Select::make('department_id') 
                    ->relationship('department', 'name')
                    ->required(),
                Forms\Components\Select::make('role_id') 
                    ->preload()
                    ->required()
                    ->relationship('roles', 'name'), 
                Forms\Components\Select::make('reporting_manager_id') 
                    // ->relationship('manager', 'first_name')
                    ->label('Reporting Manager')
                    ->preload()
                    ->relationship('manager', 'name', function ($query, $get) {
                        return $query->whereHas('roles', function ($q) {
                                $q->where('name', 'Project Leader'); // Replace with your specific role name
                            })
                            ->when($get('id'), function ($query, $id) {
                                return $query->where('id', '!=', $id); // Exclude the current record if it exists
                            });
                    }),
                Forms\Components\Radio::make('status')
                    ->required()
                    ->options(RecordStatus::class)->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phonenumber')
                    ->numeric()
                    ->sortable(),
                    Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('manager.name')
                    ->numeric()
                    ->sortable(),
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
