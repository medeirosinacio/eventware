<?php

declare(strict_types=1);

namespace Eventware\Domain\Contracts;

use Eluceo\iCal\Domain\Collection\Events;

interface CalendarSourceAdapter
{
    public function getEvents(): Events;
}
