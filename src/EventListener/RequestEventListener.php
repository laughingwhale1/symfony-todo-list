<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;

#[AsEventListener(event: RequestEvent::class, method: 'onRequestEvent')]
readonly class RequestEventListener
{
    public function __construct(
        private LoggerInterface $logger
    )
    {

    }
    public function onRequestEvent(RequestEvent $event)
    {
        $this->logger->info('Request event');
    }
}