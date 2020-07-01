<?php

namespace App\Ecommerce\PromoCodes;

use MadWeb\Enum\Enum;

/**
 * @method static PromoCodeUsedTypesEnum UNLIMITED()
 * @method static PromoCodeUsedTypesEnum ONCE()
 * @method static PromoCodeUsedTypesEnum ONCE_PERSON()
 */
final class PromoCodeUsedTypesEnum extends Enum
{
    const UNLIMITED = 'Unlimited';
    const ONCE = 'Once';
    const ONCE_PERSON = 'Once per person';
}
