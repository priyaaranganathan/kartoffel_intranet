<?php
namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
 
class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;
    protected static ?string $title = 'Dashboard';

    public function filtersForm(Form $form): Form
    {
        // return $form;
        return $form
            ->schema([
                DatePicker::make('start_date'),
                DatePicker::make('end_date')
            ]);
    }
}