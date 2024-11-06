<?php
namespace App\Filament\Resources\InvoiceResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $recordTitleAttribute = 'payment_date';
    protected static ?string $title = 'Payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('$')
                    ->maxValue(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->remaining_balance;
                    })
                    ->default(function (RelationManager $livewire) {
                        return $livewire->getOwnerRecord()->remaining_balance;
                    }),

                Forms\Components\DatePicker::make('payment_date')
                    ->required()
                    ->default(now()),

                Forms\Components\Select::make('payment_method')
                    ->required()
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'cash' => 'Cash',
                        'check' => 'Check',
                        'other' => 'Other',
                    ]),

                Forms\Components\TextInput::make('transaction_reference')
                    ->maxLength(255),

                Forms\Components\Textarea::make('notes')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                    ->money()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),

                Tables\Columns\TextColumn::make('transaction_reference')
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'bank_transfer' => 'Bank Transfer',
                        'credit_card' => 'Credit Card',
                        'cash' => 'Cash',
                        'check' => 'Check',
                        'other' => 'Other',
                    ]),
                Tables\Filters\DateFilter::make('payment_date'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->after(function (Model $record) {
                        // Update invoice status if fully paid
                        $invoice = $record->invoice;
                        if ($invoice->remaining_balance <= 0) {
                            $invoice->update(['status' => 'paid']);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('payment_date', 'desc');
    }
}