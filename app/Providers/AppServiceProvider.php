<?php

namespace App\Providers;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\ServiceProvider;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        TextInput::configureUsing(function (TextInput $textInput) {
            $textInput->inlineLabel();
        });
        Select::configureUsing(function (Select $select) {
            $select->inlineLabel();
        });
        DatePicker::configureUsing(function (DatePicker $datePicker) {
            $datePicker->inlineLabel();
        });
        Toggle::configureUsing(function (Toggle $toggle) {
            $toggle->inlineLabel();
        });
        Radio::configureUsing(function (Radio $radio) {
            $radio->inlineLabel();
        });
    }
}
