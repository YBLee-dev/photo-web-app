<?php


namespace App\Photos\Photos;


use MadWeb\Enum\Enum;

class PhotoTypeEnum extends Enum
{
    const EMPTY_TEMPLATE = 'Empty template';
    const ORIGINAL = 'Original';
    const PROOF = 'Proof image';
    const CROPPED_FACE = 'Cropped face';
    const MINI_WALLET_COLLAGE = 'Mini wallet collage';

    const COMMON_CLASS_PHOTO = 'Common class photo';
    const PERSONAL_CLASS_PHOTO = 'Personal class photo';
    const STAFF_PHOTO = 'Staff common photo';
    const SCHOOL_PHOTO = 'School common photo';

    const ID_CARD_PORTRAIT = 'ID card portrait';
    const ID_CARD_LANDSCAPE = 'ID card landscape';

    const FREE_GIFT = 'Free gift';
    const PRINTABLE = 'Printable photo';
}
