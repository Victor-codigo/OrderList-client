<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OnKernelResponseSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::RESPONSE => ['__invoke']];
    }

    public function __invoke(ResponseEvent $event)
    {
        $this->addTokenSessionCookie($event->getRequest(), $event->getResponse());
    }

    private function addTokenSessionCookie(Request $request, Response $response): void
    {
        if (!$request->cookies->has(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)) {
            return;
        }

        $tokenSessionCookie = new Cookie(
            HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME,
            $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME),
            time() + (86400 * 180),
            '/',
            HTTP_CLIENT_CONFIGURATION::CLIENT_DOMAIN,
            true,
            false,
            false,
            Cookie::SAMESITE_STRICT
        );

        $response->headers->setCookie($tokenSessionCookie);
    }
}
