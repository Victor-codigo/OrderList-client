<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Twig\Components\NavigationBar\NavigationBarDto;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class OnKernelControllerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['__invoke']];
    }


    public function __invoke(ControllerEvent $event): void
    {
        $this->loadTwigGlobals();
    }

    private function loadTwigGlobals(): void
    {
        $navigationBarComponentData = new NavigationBarDto(
            'OrderListTile'
        );

        $this->twig->addGlobal('NavigationBarComponent', $navigationBarComponentData);
    }
}
