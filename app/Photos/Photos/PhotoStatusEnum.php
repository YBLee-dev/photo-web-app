<?php


namespace App\Photos\Photos;


use MadWeb\Enum\Enum;

class PhotoStatusEnum extends Enum
{
    const INITIAL_PROCESSING = "Initial processing";
    const TEMPLATE = "Empty template photo";
    const ADDED_MANUALLY = "Manually added";
    const BROKEN = "Broken";
    const READY = "Ready";
}
