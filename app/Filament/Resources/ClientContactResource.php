<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\RecordStatus;
use App\Models\ClientContact;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\ClientContactResource\Pages;
use App\Filament\Resources\ClientContactResource\RelationManagers;

class ClientContactResource extends Resource
{
    protected static ?string $model = ClientContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord:true)
                    ->required(),
                Forms\Components\TextInput::make('contact')
                    ->Label('Contact Number')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('client_id') 
                    ->relationship('client', 'name')
                    ->required(), 
                Forms\Components\Radio::make('is_primary_contact')
                    ->Label('Primary Contact?')
                    ->options([
                        "1" => 'Yes', "0"=> 'No'
                    ])
                    ->required(),
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
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contact Number')
                    ->searchable(),
                // Tables\Columns\TextColumn::make('client_id')
                //     ->numeric()
                //     ->sortable(),
                Tables\Columns\IconColumn::make('is_primary_contact')
                    ->boolean(),
                // Tables\Columns\IconColumn::make('status')
                //     ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(), 
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ManageClientContacts::route('/'),
        ];
    }
}
