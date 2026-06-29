<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum LinkType: string implements HasLabel
{
    case Product = 'product';
    case Category = 'category';
    case Url = 'url';

    public function getLabel(): string
    {
        return match ($this) {
            self::Product => 'Product',
            self::Category => 'Category',
            self::Url => 'Custom URL',
        };
    }
}
