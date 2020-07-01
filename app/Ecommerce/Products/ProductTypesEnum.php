<?php

namespace App\Ecommerce\Products;

use MadWeb\Enum\Enum;

/**
 * @method static ProductTypesEnum PRINTABLE()
 * @method static ProductTypesEnum DIGITAL()
 * @method static ProductTypesEnum DIGITAL_FULL()
 * @method static ProductTypesEnum RETOUCH()
 * @method static ProductTypesEnum SINGLE_DIGITAL()
 */
final class ProductTypesEnum extends Enum
{
    const PRINTABLE = 'Printable';
    const DIGITAL = 'Digital';
    const DIGITAL_FULL = 'Digital Full';
    const RETOUCH = 'Retouch';
    const SINGLE_DIGITAL = 'Digital Single';
}
