<?php

declare(strict_types=1);

namespace Eventware\Application\Calendars\UFC;

use Eluceo\iCal\Domain\Collection\Events;
use Eventware\Domain\Contracts\CalendarSourceAdapter;
use Eventware\Domain\Contracts\CalendarProvider;

class UFCProvider implements CalendarProvider
{
    public function __construct(
        private readonly CalendarSourceAdapter $calendarSource,
    ) {
    }

    public function fetchEvents(): Events
    {
        return $this->calendarSource->getEvents();
    }
}
