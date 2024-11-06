<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use App\Models\Invoice;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\Concerns\InteractsWithPageTable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class InvoiceStats extends BaseWidget
{
    use InteractsWithPageTable;
    protected static ?string $pollingInterval = null;
    public ?string $filter = 'today';
    
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }
    protected function getStats(): array
    {
        $activeFilter = $this->filter;
        
        $invoiceData = Trend::model(Invoice::class)
                    ->between(
                        start: now()->subYear(),
                        end: now(),
                    )
                    ->perMonth()
                    ->count();
        return [
            Stat::make('All Invoices', Invoice::count())
            ->chart(
                $invoiceData
                    ->map(fn (TrendValue $value) => $value->aggregate)
                    ->toArray()
            ),
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
