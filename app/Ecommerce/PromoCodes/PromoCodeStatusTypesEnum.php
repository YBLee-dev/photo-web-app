<?php

namespace App\Ecommerce\PromoCodes;

use MadWeb\Enum\Enum;

/**
 * @method static PromoCodeStatusTypesEnum EXPIRED()
 * @method static PromoCodeStatusTypesEnum USED()
 * @method static PromoCodeStatusTypesEnum ACTIVE()
 */
final class PromoCodeStatusTypesEnum extends Enum
{
    const EXPIRED = 'Expired';
    const USED = 'Used';
    const ACTIVE = 'Active';
}
