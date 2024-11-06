<?php

namespace App\Filament\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvoiceOverview extends BaseWidget
{

   
    protected function getStats(): array
    {
        return [
                Stat::make('All Invoices', Invoice::count()),
                // Stat::make('Draft ', Invoice::where('status', operator: 'draft')->count()),
                // Stat::make('Sent',  Invoice::where('status', operator: 'sent')->count()),
                // Stat::make('Paid',  Invoice::where('status', operator: 'paid')->count()),
                // Stat::make('Received',  Invoice::where('status', operator: 'paid')->sum('received_amount'))
                //     ->descriptionColor('success'),
                // Stat::make('To Receive',  Invoice::where('status', operator: 'sent')->sum('total_amount')),
                Stat::make('Invoices Due', Invoice::where('status','sent')->whereDate('due_date', '>=', now())->count()),
                Stat::make('Invoices Overdue', Invoice::where('status','sent')->whereDate('due_date', '<', now())->count()),
        ];
    }
}
