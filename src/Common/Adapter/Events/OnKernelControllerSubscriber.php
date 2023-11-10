<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\NavigationBar\NavigationBarDto;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestGroupNameException;
use Common\Adapter\Events\Exceptions\RequestShopNameException;
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
        private Endpoints $endpoints,
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
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
        $groupData = $this->loadGroupData($request->attributes, $tokenSession);

        $requestDto = new RequestDto(
            $this->loadTokenSession($request),
            $this->loadGroupName($request->attributes),
            $groupData,
            $this->loadShopData($request->attributes, $groupData->id ?? null, $tokenSession),
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

    private function loadGroupData(ParameterBag $attributes, string $tokenSession): GroupDataResponse|null
    {
        if (!$attributes->has('group_name')) {
            return null;
        }

        $groupNameDecoded = $this->decodeUrlName($attributes->get('group_name'));
        $groupData = $this->endpoints->groupGetDataByName($groupNameDecoded, $tokenSession);

        if (!empty($groupData['errors'])) {
            throw RequestGroupNameException::fromMessage('Could not get group data');
        }

        return new GroupDataResponse(
            $groupData['data']['group_id'],
            $groupData['data']['name'],
            $groupData['data']['description'],
            $groupData['data']['image'],
            $groupData['data']['created_on'],
        );
    }

    private function loadTokenSession(Request $request): string|null
    {
        if (!$request->cookies->has(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)) {
            return null;
        }

        return $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
    }

    private function loadGroupName(ParameterBag $attributes): string|null
    {
        if (!$attributes->has('group_name')) {
            return null;
        }

        return $this->decodeUrlName($attributes->get('group_name'));
    }

    private function loadShopData(ParameterBag $attributes, string|null $groupId, string $tokenSession): ShopDataResponse|null
    {
        if (null === $groupId || !$attributes->has('shop_name')) {
            return null;
        }

        $shopNameDecoded = $this->decodeUrlName($attributes->get('shop_name'));
        $shopData = $this->endpoints->shopsGetData($groupId, null, null, $shopNameDecoded, $tokenSession);

        if (!empty($shopData['errors'])) {
            throw RequestShopNameException::fromMessage('Could not get shop data');
        }

        return new ShopDataResponse(
            $shopData['data'][0]['id'],
            $shopData['data'][0]['group_id'],
            $shopData['data'][0]['name'],
            $shopData['data'][0]['description'],
            $shopData['data'][0]['image'],
            $shopData['data'][0]['created_on'],
        );
    }
}
