<?php

namespace App\Ecommerce\Orders;

use MadWeb\Enum\Enum;

/**
 * @method static OrderStatusEnum NEW()
 * @method static OrderStatusEnum CLOSED()
 * @method static OrderStatusEnum NOT_PAID()
 */
final class OrderStatusEnum extends Enum
{
    const __default = self::NEW;

    const NEW = 'New';
    const PRINTING = 'Printing';
    const READY = 'Ready';
    const CLOSED = 'Closed';

}
