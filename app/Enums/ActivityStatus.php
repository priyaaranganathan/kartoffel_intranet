<?php

namespace App\Enums;

// use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ActivityStatus: string implements HasLabel, HasColor
{
    case REVIEW = 'review';
    case NOTSTARTED = 'not started';
    case INPROGRESS = 'in progress';
    case ONHOLD = 'on hold';
    case COMPLETED = 'completed';

 
    public function getLabel(): ?string
    {
        return match ($this) {
            self::REVIEW => 'Review',
            self::NOTSTARTED => 'Not Started',
            self::INPROGRESS => 'In Progress',
            self::ONHOLD => 'On Hold',
            self::COMPLETED => 'Completed'
        };
    }
 
    public function getColor(): string|array|null
    {
        return match ($this) {
            self::REVIEW => 'gray',
            self::NOTSTARTED => 'warning',
            self::INPROGRESS => 'primary',
            self::ONHOLD => 'gray',
            self::COMPLETED => 'success'
        };
    }

    public static function getValues(): array {
        return [
            self::REVIEW,
            self::NOTSTARTED,
            self::INPROGRESS,
            self::ONHOLD,
            self::COMPLETED
        ];
    }
 
    // public function getIcon(): ?string
    // {
    //     return match ($this) {
    //         self::ACTIVE => 'heroicon-m-check',
    //         self::INACTIVE => 'heroicon-m-check'
    //     };
    // }
}
