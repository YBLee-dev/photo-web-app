<?php

namespace App\IDCard;

use MadWeb\Enum\Enum;

/**
 * @method static CardIDTypesEnum PORTRAIT()
 * @method static CardIDTypesEnum LANDSCAPE()
 */
final class CardIDTypesEnum extends Enum
{
    const PORTRAIT = 'portrait';
    const LANDSCAPE = 'landscape';
}
