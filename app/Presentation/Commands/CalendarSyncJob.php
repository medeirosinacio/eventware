<?php

namespace Eventware\Presentation\Commands;

use Illuminate\Console\Command;
use Psr\Container\ContainerInterface;

class CalendarSyncJob extends Command
{
    protected $signature = 'calendars:sync';

    protected $description = 'Sync external calendars';

    public function handle(ContainerInterface $container): void
    {
        $this->info('Syncing calendars...');

        foreach ($container->get('config')->array('calendars.external_calendars') as $calendar => $settings) {
            $this->info("Sync for calendar: $calendar");
            $provider = $container->get($settings['provider']);
            $events = $provider->fetchEvents();
            //    $provider->updateEvents($events);
            $this->info("Synced " . count($events) . " events for calendar: $calendar");
        }
    }
}
