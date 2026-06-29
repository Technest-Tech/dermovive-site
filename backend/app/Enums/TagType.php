<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TagType: string implements HasLabel
{
    case SkinType = 'skin_type';
    case Concern = 'concern';
    case Highlight = 'highlight';

    public function getLabel(): string
    {
        return match ($this) {
            self::SkinType => 'Skin type',
            self::Concern => 'Concern',
            self::Highlight => 'Highlight',
        };
    }
}
