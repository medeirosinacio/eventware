<?php

declare(strict_types=1);

namespace Eventware\Console\Commands\Development;

use DateTimeImmutable;
use Eluceo\iCal\Domain\Entity\Calendar;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\Location;
use Eluceo\iCal\Domain\ValueObject\SingleDay;
use Eluceo\iCal\Presentation\Factory\CalendarFactory;

final class DebugCommand
{
    public static function run(): true
    {
        $event = (new Event)
            ->setSummary('Christmas Eve')
            ->setDescription('Lorem Ipsum Dolor... Test Sync')
            ->setLocation(
                new Location('Porto Alegre, RS, Brazil')
            )
            ->setOccurrence(
                new SingleDay(
                    new Date(
                        DateTimeImmutable::createFromFormat('Y-m-d', '2030-12-24') ?: new DateTimeImmutable
                    )
                )
            );
        $calendar = new Calendar([$event]);
        $iCalendarComponent = (new CalendarFactory)->createCalendar($calendar);

        file_put_contents('/app/runtime/tmp/calendar.ics', (string) $iCalendarComponent);

        return true;
    }
}
