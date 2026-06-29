<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum ProductBadge: string implements HasColor, HasLabel
{
    case NewArrival = 'new';
    case Bestseller = 'bestseller';
    case Limited = 'limited';

    public function getLabel(): string
    {
        return match ($this) {
            self::NewArrival => 'New',
            self::Bestseller => 'Bestseller',
            self::Limited => 'Limited edition',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NewArrival => 'success',
            self::Bestseller => 'primary',
            self::Limited => 'warning',
        };
    }
}
