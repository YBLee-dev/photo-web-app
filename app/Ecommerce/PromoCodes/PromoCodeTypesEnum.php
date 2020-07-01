<?php

namespace App\Ecommerce\PromoCodes;

use MadWeb\Enum\Enum;

/**
 * @method static PromoCodeTypesEnum PERCENT()
 */
final class PromoCodeTypesEnum extends Enum
{
    const __default = self::PERCENT;

    const PERCENT = '%';
    const MONEY = '$';
}
