<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;

class EditOrganisation extends Page implements HasForms
{

    use InteractsWithForms;

    public ?array $data = []; 
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.edit-organisation';

    public function mount(): void 
    {
        // $this->form->fill();
        $this->form->fill(auth()->user()->organisation->attributesToArray());
    }
 
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ])
            ->statePath('data');
    } 
}
