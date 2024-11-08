<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Employee;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Enums\RecordStatus;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EmployeeResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use Spatie\Permission\Models\Role;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Organisation';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')
                    ->required(),
                Forms\Components\TextInput::make('last_name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord:true)
                    ->required(),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->required(fn ($record) => is_null($record)) 
                    ->password()
                    ->hidden(fn ($record) => $record !== null),
                Forms\Components\TextInput::make('contact')
                    ->unique(ignoreRecord:true)
                    ->required(),
                Forms\Components\Select::make('department_id') 
                    ->relationship('department', 'name')
                    ->required(),
                Forms\Components\Select::make('role_id') 
                    ->preload()
                    ->required()
                    ->relationship('roles', 'name'), 
                // Forms\Components\Select::make('reporting_manager_id')
                //     ->relationship('manager', fn ($query) => $query->select('id', 'first_name', 'last_name'))
                //                         ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                //                         ->options(function (Forms\Get $get, ?Employee $record) {
                //                             $currentEmployeeDesignation = $record ? $record->designation : null;
                //                             if (!$currentEmployeeDesignation && $get('designation_id')) {
                //                                 $currentEmployeeDesignation = \App\Models\Designation::find($get('designation_id'));
                //                             }
                //                             if (!$currentEmployeeDesignation) {
                //                                 return [];
                //                             }
                //                             $currentLevel = $currentEmployeeDesignation->level;
                //                             return Employee::whereHas('designation', function ($query) use ($currentLevel) {
                //                                 $query->where('level', $currentLevel - 1);
                //                             })
                //                             ->when($record, function ($query) use ($record) {
                //                                 return $query->where('id', '!=', $record->id);
                //                             })
                //                             ->get()
                //                             ->mapWithKeys(function ($employee) {
                //                                 return [$employee->id => "{$employee->first_name} {$employee->last_name}"];
                //                             });
                //                         })
                //                         ->searchable()
                //                         ->preload(),
                Forms\Components\Select::make('reporting_manager_id') 
                    // ->relationship('manager', 'first_name')
                    ->label('Reporting Manager')
                    ->preload()
                    ->relationship('manager', 'first_name', function ($query, $get) {
                        return $query->whereHas('roles', function ($q) {
                                $q->where('name', 'Project Leader'); // Replace with your specific role name
                            })
                            ->when($get('id'), function ($query, $id) {
                                return $query->where('id', '!=', $id); // Exclude the current record if it exists
                            });
                    }),
                Forms\Components\Radio::make('status')
                    ->required()
                    ->options(RecordStatus::class)->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contact')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('manager.first_name')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }

    public static function getActions(): array
    {
        return [
            Tables\Actions\Action::make('updatePassword')
                ->label('Update Password')
                ->action(function (Employee $record) {
                    // Open the password update modal
                    return [
                        'modal' => true,
                        'form' => [
                            Forms\Components\TextInput::make('password')
                                ->label('New Password')
                                ->required()
                                ->password(),
                            Forms\Components\TextInput::make('password_confirmation')
                                ->label('Confirm Password')
                                ->required()
                                ->password(),
                        ],
                        'action' => function ($data) use ($record) {
                            // Update the password logic
                            $record->password = bcrypt($data['password']);
                            $record->save();

                            // Return a success message
                            return 'Password updated successfully!';
                        },
                    ];
                }),
        ];
    }
}
