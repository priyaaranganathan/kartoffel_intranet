<?php
namespace App\Filament\Resources;

use PDF;
use Filament\Forms;
use Filament\Tables;
use App\Models\Invoice;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Mail\InvoiceMail;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use App\Filament\Resources\InvoiceResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\InvoiceResource\Widgets\InvoiceStats;

class InvoiceResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Finance';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
            'download',
            'cancel',
            'update_paid'
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('client_id')
                    // ->relationship('client', 'name')
                    ->options(function (Get $get) {
                        return \App\Models\Client::where('status', 'active')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('project_id', null)),

                Forms\Components\Select::make('project_id')
                    ->options(function (Get $get) {
                        $clientId = $get('client_id');
                        if (!$clientId) return [];
                        
                        return \App\Models\Project::where('client_id', $clientId)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->live(),

                Forms\Components\TextInput::make('invoice_number')
                    ->required()
                    ->default(fn () => 'INV-' . str_pad(Invoice::count() + 1, 5, '0', STR_PAD_LEFT)),

                Forms\Components\DatePicker::make('issue_date')
                    ->required()
                    ->default(now()),

                Forms\Components\DatePicker::make('due_date')
                    ->required()
                    ->default(now()->addDays(10)),

                // Invoice Items Repeater
                Forms\Components\Repeater::make('items')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('milestone_id')
                            ->options(function (Get $get) {
                                $projectId = $get('../../project_id');
                                if (!$projectId) return [];
                                
                                return \App\Models\Milestone::where('project_id', $projectId)
                                    ->where('status', 'completed')
                                    ->pluck('title', 'id');
                            })
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                if ($state) {
                                    $milestone = \App\Models\Milestone::find($state);
                                    $notes = 'amount'. $milestone->payment_amount .' Milestone '.$milestone->title;
                                    $set('amount', $milestone->payment_amount ?? 0);
                                    $set('description', $milestone->title);
                                    $set('notes', $notes);
                                }
                            }),

                        Forms\Components\TextInput::make('description')
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->numeric()
                            ->required()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                // Trigger subtotal and total calculation
                                $items = $get('items') ?? [];
                                $subtotal = collect($items)->sum('amount');
                                $taxPercentage = $get('tax_rate') ?? 0;
                                $taxAmount = ($subtotal * $taxPercentage) / 100;
                                $total = $subtotal + $taxAmount;
                                
                                $set('subtotal', $subtotal);
                                $set('tax_amount', $taxAmount);
                                $set('total_amount', $total);
                            }),
                    ])
                    ->columns(3)
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Trigger subtotal and total calculation
                        $items = $get('items') ?? [];
                        $subtotal = collect($items)->sum('amount');
                        $taxPercentage = $get('tax_rate') ?? 0;
                        $taxAmount = ($subtotal * $taxPercentage) / 100;
                        $total = $subtotal + $taxAmount;
                        
                        $set('subtotal', $subtotal);
                        $set('tax_amount', $taxAmount);
                        $set('total_amount', $total);
                    }),

                // Subtotal
                Forms\Components\TextInput::make('subtotal')
                    ->numeric()
                    ->readonly()
                    ->prefix('$')
                    ->default(0)
                    ->reactive(),

                // Tax Percentage (User Input)
                Forms\Components\TextInput::make('tax_rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->reactive()
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        // Trigger tax amount and total calculation when tax percentage changes
                        $subtotal = $get('subtotal') ?? 0;
                        $taxPercentage = $get('tax_rate') ?? 0;
                        $taxAmount = ($subtotal * $taxPercentage) / 100;
                        $total = $subtotal + $taxAmount;

                        $set('tax_amount', $taxAmount);
                        $set('total_amount', $total);
                    }),

                // Tax Amount
                Forms\Components\TextInput::make('tax_amount')
                    ->numeric()
                    ->readonly()
                    ->prefix('$')
                    ->reactive(),

                // Total Amount
                Forms\Components\TextInput::make('total_amount')
                    ->numeric()
                    ->readonly()
                    ->prefix('$')
                    ->reactive(),

                Forms\Components\TextInput::make('notes')
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('client.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date(),
                Tables\Columns\TextColumn::make('due_date')
                    ->since()
                    ->extraAttributes(function (?Invoice $record) {
                        return $record->due_date < now()
                            ? ['class' => 'bg-blue-600']
                            : [];
                    }),
                Tables\Columns\TextColumn::make('total')
                    ->money('INR'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'sent' => 'warning',
                        'paid' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('client')
                    ->relationship('client', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'sent' => 'Sent',
                        'paid' => 'Paid',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\Action::make('download')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function (Invoice $record) {
                            return response()->streamDownload(function () use ($record) {
                                echo self::generatePdf($record)->output();
                            }, "{$record->invoice_number}.pdf");
                        }),

                    Tables\Actions\Action::make('send_email')
                        ->icon('heroicon-o-envelope')
                        ->form([
                            Forms\Components\TextInput::make('email')
                                ->email()
                                ->required(),
                        ])
                        ->action(function (Invoice $record, array $data) {
                            Mail::to($data['email'])->send(new InvoiceMail($record));
                            $record->update([
                                'status' => 'sent',

                            ]);
                            Notification::make()
                                ->title('Invoice sent successfully')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('mark_as_paid')
                        ->icon('heroicon-o-banknotes')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\TextInput::make('received_amount')
                                ->numeric()
                                ->required(),
                        ])
                        ->action(function (Invoice $record, array $data) {
                            $record->update([
                                'status' => 'paid',
                                'received_amount' => $data['received_amount'],
                                'payment_received_date' => now(),
                            ]);

                            Notification::make()
                                ->title('Invoice marked as paid')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(Invoice $record) => $record->status !== 'paid'),
                    Tables\Actions\Action::make('cancel_invoice')
                        ->icon('heroicon-o-archive-box-x-mark')
                        ->requiresConfirmation()
                        ->action(function (Invoice $record) {
                            $record->update([
                                'status' => 'cancelled'
                            ]);

                            Notification::make()
                                ->title('Invoice marked as cancelled')
                                ->success()
                                ->send();
                        })
                        ->visible(fn(Invoice $record) => $record->status !== 'paid'),
                ])
            ]);
            // ->bulkActions([
            //     Tables\Actions\BulkActionGroup::make([
            //         Tables\Actions\DeleteBulkAction::make(),
            //     ]),
            // ]);
    }

    public static function generatePdf(Invoice $invoice)
    {

        // dd($invoice);
        $pdf = Pdf::loadView('pdf.invoice', [
            'invoice' => $invoice->load(['client', 'project', 'items.milestone']),
        ]);
        
        return $pdf;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            InvoiceStats::class,
        ];
    }
}