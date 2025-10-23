<?php

use Eventware\Domain\Enums\ExternalCalendars;

return [
    'external_calendars' => [
        ExternalCalendars::UFC->name => [
            'provider' => \Eventware\Application\Calendars\UFC\UFCProvider::class,
        ],
    ],
];
