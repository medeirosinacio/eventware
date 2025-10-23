<?php

declare(strict_types=1);

namespace Eventware\Application\Calendars\UFC;

use Eluceo\iCal\Domain\Collection\Events;
use Eluceo\iCal\Domain\Collection\EventsArray;
use Eluceo\iCal\Domain\Entity\Event;
use Eluceo\iCal\Domain\ValueObject\Date;
use Eluceo\iCal\Domain\ValueObject\MultiDay;
use Eventware\Domain\Contracts\CalendarSourceAdapter;
use Illuminate\Http\Client\PendingRequest;
use Symfony\Component\DomCrawler\Crawler;

class UFCSourceAdapter implements CalendarSourceAdapter
{
    public function __construct(
        private readonly PendingRequest $client,
    ) {
    }

    public function getEvents(): Events
    {
        $eventURLs = $this->fetchEventURLs();
        $events = [];

        foreach ($eventURLs as $url) {
            $details = $this->fetchEventDetails($url);

            $event = new Event();
            $event->setSummary($details['name']);
            $event->setOccurrence(
                new MultiDay(
                    new Date((new \DateTimeImmutable())->setTimestamp((int) $details['date'])),
                    new Date((new \DateTimeImmutable())->setTimestamp((int) $details['date']))
                )
            );
            $event->setLocation($details['location'] ?? '');

            // Adiciona fights como descrição
            $description = implode("\n", array_merge(
                $details['mainCard'],
                $details['prelims'],
                $details['earlyPrelims'],
                $details['fightCard']
            ));
            $event->setDescription($description);

            $events[] = $event;
        }

        return new EventsArray($events);
    }

    private function fetchEventURLs(): array
    {
        $pages = [
            'https://www.ufc.com/events?page=0',
            'https://www.ufc.com/events?page=1',
        ];

        $eventURLs = [];
        foreach ($pages as $pageURL) {
            $eventURLs = array_merge($eventURLs, $this->extractEventURLsFromPage($pageURL));
        }
        return $eventURLs;
    }

    private function extractEventURLsFromPage(string $url): array
    {
        $response = $this->client->get($url);
        $crawler = new Crawler($response->body());

        $eventURLElements = $crawler->filter('.c-card-event--result__headline');
        $eventURLs = [];
        foreach ($eventURLElements as $element) {
            $headline = new Crawler($element);
            $firstChild = $headline->children()->first();
            $href = $firstChild->attr('href');
            if ($href) {
                $eventURLs[] = 'https://www.ufc.com' . $href;
            }
        }
        return $eventURLs;
    }

    private function fetchEventDetails(string $url): array
    {
        $response = $this->client->get($url);
        $crawler = new Crawler($response->body());

        $headlinePrefix = $crawler->filter('.c-hero__headline-prefix')->count()
            ? trim($crawler->filter('.c-hero__headline-prefix')->text())
            : '';

        $headline = $crawler->filter('.c-hero__headline')->count()
            ? trim(preg_replace('/\s\s+/', ' ', $crawler->filter('.c-hero__headline')->text()))
            : '';

        $date = $crawler->filter('.c-hero__headline-suffix')->count()
            ? $crawler->filter('.c-hero__headline-suffix')->attr('data-timestamp')
            : null;

        $venueNode = $crawler->filter('.field--name-venue');
        $location = $venueNode->count()
            ? implode(', ', array_filter(array_map('trim', explode("\n", str_replace(',', '', $venueNode->text())))))
            : '';

        $details = [
            'name' => html_entity_decode("{$headlinePrefix}: {$headline}", ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'url' => $url,
            'date' => $date,
            'location' => html_entity_decode($location, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
            'mainCard' => $this->extractFightList($crawler, '#main-card .l-listing__item'),
            'prelims' => $this->extractFightList($crawler, '#prelims-card .l-listing__item'),
            'earlyPrelims' => $this->extractFightList($crawler, '#early-prelims .l-listing__item'),
            'fightCard' => $this->extractFightList($crawler, '.l-listing__group--bordered .l-listing__item'),
        ];

        return $details;
    }

    private function extractFightList(Crawler $crawler, string $selector): array
    {
        $elements = $crawler->filter($selector);
        $fights = [];
        foreach ($elements as $li) {
            $fights[] = $this->convertLiToStr(new Crawler($li));
        }
        return array_filter($fights);
    }

    private function convertLiToStr(Crawler $li): string
    {
        $boutNode = $li->filter('.c-listing-fight__class-text');
        $bout = $boutNode->count() ? $boutNode->text() : null;

        if (! $bout) {
            $textNode = $li->filter('.field--name-node-title');
            $textContent = $textNode->count() ? trim($textNode->text()) : '';
            $textContent = str_replace(' vs ', ' vs. ', $textContent);
            return $textContent ? '• ' . $textContent : '';
        }

        $weightClass = match (true) {
            str_contains($bout, 'Strawweight') => '115',
            str_contains($bout, 'Flyweight') => '125',
            str_contains($bout, 'Bantamweight') => '135',
            str_contains($bout, 'Featherweight') => '145',
            str_contains($bout, 'Lightweight') => '155',
            str_contains($bout, 'Welterweight') => '170',
            str_contains($bout, 'Middleweight') => '185',
            str_contains($bout, 'Light Heavyweight') => '205',
            str_contains($bout, 'Heavyweight') => '265',
            str_contains($bout, 'Catchweight') => 'CW',
            default => '',
        };

        $red = $li->filter('.c-listing-fight__corner-name--red')->count()
            ? trim(preg_replace('/\s+/', ' ', str_replace("\n", '', $li->filter('.c-listing-fight__corner-name--red')->text())))
            : '___';
        $blue = $li->filter('.c-listing-fight__corner-name--blue')->count()
            ? trim(preg_replace('/\s+/', ' ', str_replace("\n", '', $li->filter('.c-listing-fight__corner-name--blue')->text())))
            : '___';

        $ranks = [];
        foreach ($li->filter('.js-listing-fight__corner-rank.c-listing-fight__corner-rank') as $rankNode) {
            $ranks[] = trim((new Crawler($rankNode))->text());
        }
        $redRankStr = isset($ranks[0]) && $ranks[0] ? " ({$ranks[0]})" : '';
        $blueRankStr = isset($ranks[1]) && $ranks[1] ? " ({$ranks[1]})" : '';

        $fightStr = "• {$red}{$redRankStr} vs. {$blue}{$blueRankStr} @{$weightClass}";
        return html_entity_decode($fightStr, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
