<?php

namespace App\Ecommerce\Cart;

use MadWeb\Enum\Enum;

/**
 * @method static CartItemTypesEnum PRODUCT()
 * @method static CartItemTypesEnum PACKAGE()
 */
final class CartItemTypesEnum extends Enum
{
    const PRODUCT = 'product';
    const PACKAGE = 'package';
}
