<?php

namespace App\Filament\Resources\ClientResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\RecordStatus;
use App\Models\ClientContact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';

    public function form(Form $form): Form
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
                    ->default(fn ($record) => $record?->client_id ?? static::getOwnerRecord()->id)
                    ->disabled()
                    ->required(), 
                Forms\Components\Radio::make('is_primary_contact')
                    ->Label('Primary Contact?')
                    ->options([
                        "1" => 'Yes', "0"=> 'No'
                    ])
                    ->default('0')
                    ->required(),
                Forms\Components\Radio::make('status')
                    ->required()
                    ->options(RecordStatus::class)->default('active'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->label('Contact Number')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_primary_contact')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status')
                    ->badge(), 
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
