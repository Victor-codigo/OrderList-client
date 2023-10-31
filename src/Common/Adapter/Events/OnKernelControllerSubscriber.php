<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Controller\Request\RequestDto;
use App\Twig\Components\NavigationBar\NavigationBarDto;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestGroupNameException;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class OnKernelControllerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private Endpoints $endpoints
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['__invoke']];
    }

    public function __invoke(ControllerEvent $event): void
    {
        $this->loadRequestDto($event->getRequest());
        $this->loadTwigGlobals();
    }

    private function loadRequestDto(Request $request): void
    {
        $requestDto = new RequestDto(
            $this->loadTokenSession($request),
            $this->loadGroupData($request->attributes, $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)),
            $request
        );

        $request->attributes->set('requestDto', $requestDto);
    }

    private function decodeUrlName(string|null $name): string|null
    {
        if (null === $name) {
            return null;
        }

        return str_replace('-', ' ', $name);
    }

    private function loadTwigGlobals(): void
    {
        $navigationBarComponentData = new NavigationBarDto(
            'OrderListTile'
        );

        $this->twig->addGlobal('NavigationBarComponent', $navigationBarComponentData);
    }

    private function loadGroupData(ParameterBag $attributes, string $tokenSession): array|null
    {
        if (!$attributes->has('group_name')) {
            return null;
        }

        $groupNameDecoded = $this->decodeUrlName($attributes->get('group_name'));
        $groupData = $this->endpoints->groupGetDataByName($groupNameDecoded, $tokenSession);

        if (!empty($groupData['errors'])) {
            throw RequestGroupNameException::fromMessage('Could not get group data');
        }

        return $groupData['data'];
    }

    private function loadTokenSession(Request $request): string|null
    {
        if (!$request->cookies->has(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)) {
            return null;
        }

        return $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
    }
}
