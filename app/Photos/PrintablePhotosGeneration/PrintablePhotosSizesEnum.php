<?php


namespace App\Photos\PrintablePhotosGeneration;


use MadWeb\Enum\Enum;

class PrintablePhotosSizesEnum extends Enum
{
    /**
     * DON'T UPDATE SIZES ORDER
     * IT USED FOR PRINTABLE PHOTOS ORDERING
     */
    const ONE_8x10 = '1 - 8x10';
    const TWO_5x7 = '2 - 5x7';
    const ONE_5x7_AND_TWO_3_5x5 = '1 - 5x7 and 2 - 3.5x5';
    const ONE_5x7_AND_4_WALLETS = '1 - 5x7 and 4 - Wallets';
    const FOUR_3_5x5 = '4 - 3.5x5';
    const TWO_3_5x5_AND_FOUR_WALLETS = '2 - 3.5x5 and 4 - Wallets';
    const SET_OF_8_WALLETS = 'Set of 8 wallets';
}
