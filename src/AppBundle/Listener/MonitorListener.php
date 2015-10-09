<?php
namespace AppBundle\Listener;

use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use InfluxDB\Client;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class MonitorListener
{
    private $client;
    private $stopwatch;

    public function __construct(Client $client, Stopwatch $stopwatch = null)
    {
        $this->client = $client;
        $this->stopwatch = $stopwatch;
    }

    public function startStopwatch(GetResponseEvent $event)
    {
        $routeName = $event->getRequest()->attributes->get("_route");

        $this->stopwatch->start($routeName);
    }

    public function stopStopwatch(FilterResponseEvent $event)
    {
        $routeName = $event->getRequest()->attributes->get("_route");

        $events = $this->stopwatch->stop($routeName);

        foreach ($events->getPeriods() as $measure) {
            $this->client->mark($routeName, [
                "start" => $measure->getStartTime(),
                "end" => $measure->getEndTime(),
                "duration" => $measure->getDuration(),
                "memory" => $measure->getMemory(),
            ]);
        }
    }
}
