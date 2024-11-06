<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions;
use App\Models\Invoice;
use Widgets\AccountWidget;
use Filament\Resources\Components\Tab;
use App\Filament\Widgets\InvoiceOverview;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InvoiceResource;
use Filament\Pages\Concerns\ExposesTableToWidgets;
use App\Filament\Resources\InvoiceResource\Widgets\InvoiceStats;

class ListInvoices extends ListRecords
{
    use ExposesTableToWidgets;
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            null => Tab::make('All'),
            'draft' => Tab::make()->query(fn ($query) => $query->where('status', 'draft')),
            'sent' => Tab::make()->query(fn ($query) => $query->where('status', 'sent')),
            'paid' => Tab::make()->query(fn ($query) => $query->where('status', 'paid')),
            'cancelled' => Tab::make()->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
    // public function getTabs(): array
    // {
    //     $tabs = [];
    
    //     $tabs[] = Tab::make('All Invoices')
    //         ->badge(Invoice::count());
    
    //     $tabs[] = Tab::make('Draft')
    //         ->badge(Invoice::where('status', operator: 'draft')->count());
        
    //     $tabs[] = Tab::make('Sent')
    //         ->badge(Invoice::where('status', operator: 'sent')->count());
       

    //     $tabs[] = Tab::make('Received')
    //         ->badge(Invoice::where('status', 'paid')->sum('total_amount'))
    //         ->icon('heroicon-o-banknotes')
    //         ->iconPosition('before');

    //     return $tabs;
    // }

    protected function getHeaderWidgets(): array
    {
        return [
            InvoiceStats::class,
        ];
    }
}
