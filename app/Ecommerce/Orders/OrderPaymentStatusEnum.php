<?php

namespace App\Ecommerce\Orders;

use MadWeb\Enum\Enum;

/**
 * @method static OrderPaymentStatusEnum PAID()
 * @method static OrderPaymentStatusEnum NOT_PAID()
 */
final class OrderPaymentStatusEnum extends Enum
{
    const __default = self::NOT_PAID;

    const PAID = 'Paid';
    const NOT_PAID = 'Not paid';
}
