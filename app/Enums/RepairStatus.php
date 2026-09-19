<?php

namespace App\Enums;

enum RepairStatus: string
{
    case NEW = 'NEW';
    case DIAGNOSTICS = 'DIAGNOSTICS';
    case WAITING_APPROVAL = 'WAITING_APPROVAL';
    case WAITING_PART = 'WAITING_PART';
    case IN_REPAIR = 'IN_REPAIR';
    case READY = 'READY';
    case ISSUED = 'ISSUED';
    case CANCELLED = 'CANCELLED';
}
