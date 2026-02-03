<?php

namespace App\Enums;

enum OfferStatus: int
{
    case NOT_STARTED = 0;
    case PENDING = 1;
    case ACCEPTED = 2;
    case REJECTED = 3;
    case OFFER_REVOKED = 4;
    case COMPLETED_PRE_JOINING = 5;
    case DAY_ONE_TICKET_ISSUED = 6;
    case REGISTERED_EMPLOYEE = 7;
}
