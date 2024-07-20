<?php

declare(strict_types=1);

namespace Eventware\Commands\Development;

final class DebugCommand
{
    public static function run(): true
    {
        $event = (new \Eluceo\iCal\Domain\Entity\Event())
            ->setSummary('Christmas Eve')
            ->setDescription('Lorem Ipsum Dolor... Test Sync')
            ->setLocation(
                new \Eluceo\iCal\Domain\ValueObject\Location('Porto Alegre, RS, Brazil')
            )
            ->setOccurrence(
                new \Eluceo\iCal\Domain\ValueObject\SingleDay(
                    new \Eluceo\iCal\Domain\ValueObject\Date(
                        \DateTimeImmutable::createFromFormat('Y-m-d', '2030-12-24')
                    )
                )
            );
        $calendar = new \Eluceo\iCal\Domain\Entity\Calendar([$event]);
        $iCalendarComponent = (new \Eluceo\iCal\Presentation\Factory\CalendarFactory())->createCalendar($calendar);

        file_put_contents('/app/runtime/tmp/calendar.ics', (string) $iCalendarComponent);

        return true;
    }
}
