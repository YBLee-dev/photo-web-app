<?php


namespace App\Processing;


use MadWeb\Enum\Enum;

class ProcessingStatusesEnum extends Enum
{
    const NEWER_STARTED = 'Never started';
    const IN_QUEUE = 'In queue';
    const IN_PROGRESS = 'In progress';
    const WAIT = 'Wait';
    const FAILED = 'Failed';
    const FINISHED = 'Finished';
}
