<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Controller\Request\RequestDto;
use App\Controller\Request\RequestRefererDto;
use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\NavigationBar\NavigationBarDto;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestGroupNameException;
use Common\Adapter\Events\Exceptions\RequestProductNameException;
use Common\Adapter\Events\Exceptions\RequestShopNameException;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\Config\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\OptionsResolver\Exception\NoConfigurationException;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

class OnKernelControllerSubscriber implements EventSubscriberInterface
{
    use CodedUrlParameter;

    public function __construct(
        private Environment $twig,
        private Endpoints $endpoints,
        private RouterInterface $router,
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
            $this->loadLocale($request),
            $request->attributes->get('group_name'),
            $request->attributes->get('shop_name'),
            $request->attributes->get('product_name'),
            $this->loadPageData($request),
            $this->loadPageItemsData($request),
            $groupData,
            $this->loadShopData($request->attributes, $groupData?->id, $tokenSession),
            $this->loadProductData($request->attributes, $groupData?->id, $tokenSession),
            $request,
            $this->loadRefererRouteName($request)
        );

        $request->attributes->set('requestDto', $requestDto);
    }

    private function loadTwigGlobals(): void
    {
        $navigationBarComponentData = new NavigationBarDto(
            'OrderListTile'
        );

        $this->twig->addGlobal('NavigationBarComponent', $navigationBarComponentData);
    }

    private function loadTokenSession(Request $request): string|null
    {
        if (!$request->cookies->has(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)) {
            return null;
        }

        return $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
    }

    private function loadLocale(Request $request): string|null
    {
        if (!$request->attributes->has('_locale')) {
            return null;
        }

        return $request->attributes->get('_locale');
    }

    private function loadPageData(Request $request): int|null
    {
        if ($request->query->has('page')) {
            return $request->query->getInt('page');
        }

        if ($request->attributes->has('page')) {
            return $request->attributes->getInt('page');
        }

        return null;
    }

    private function loadPageItemsData(Request $request): int|null
    {
        if ($request->query->has('page_items')) {
            return $request->query->getInt('page_items');
        }

        if ($request->attributes->has('page_items')) {
            return $request->attributes->getInt('page_items');
        }

        return null;
    }

    private function loadGroupData(ParameterBag $attributes, string $tokenSession): GroupDataResponse|null
    {
        $groupNameDecoded = $this->decodeUrlParameter($attributes, 'group_name');

        if (null === $groupNameDecoded) {
            return null;
        }

        $groupData = $this->endpoints->groupGetDataByName($groupNameDecoded, $tokenSession);

        if (!empty($groupData['errors'])) {
            throw RequestGroupNameException::fromMessage('Group data not found');
        }

        return GroupDataResponse::fromArray($groupData['data']);
    }

    private function loadShopData(ParameterBag $attributes, string|null $groupId, string $tokenSession): ShopDataResponse|null
    {
        if (null === $groupId) {
            return null;
        }

        $shopId = $this->loadParamShopId($attributes);
        $shopNameDecoded = $this->loadParamShopName($attributes);

        if (null === $shopId && null === $shopNameDecoded) {
            return null;
        }

        $shopData = $this->endpoints->shopsGetData($groupId, $shopId, null, $shopNameDecoded, null, $tokenSession);

        if (!empty($shopData['errors'])) {
            throw RequestShopNameException::fromMessage('Group data not found');
        }

        return ShopDataResponse::fromArray($shopData['data'][0]);
    }

    private function loadParamShopId(ParameterBag $attributes): array|null
    {
        if (!$attributes->has('shop_id')) {
            return null;
        }

        return [$attributes->get('shop_id')];
    }

    private function loadParamShopName(ParameterBag $attributes): string|null
    {
        if (!$attributes->has('shop_name')) {
            return null;
        }

        $shopNameDecoded = $this->decodeUrlParameter($attributes, 'shop_name');

        if (null === $shopNameDecoded) {
            return null;
        }

        return $shopNameDecoded;
    }

    private function loadProductData(ParameterBag $attributes, string|null $groupId, string $tokenSession): ProductDataResponse|null
    {
        if (null === $groupId) {
            return null;
        }

        $productNameDecoded = $this->decodeUrlParameter($attributes, 'product_name');

        if (null === $productNameDecoded) {
            return null;
        }

        $productData = $this->endpoints->productGetData($groupId, null, null, $productNameDecoded, null, $tokenSession);

        if (!empty($productData['errors'])) {
            throw RequestProductNameException::fromMessage('Group data not found');
        }

        return ProductDataResponse::fromArray($productData['data'][0]);
    }

    private function loadRefererRouteName(Request $request): RequestRefererDto|null
    {
        if (Config::CLIENT_DOMAIN !== $request->getHost()) {
            return null;
        }

        $urlReferer = $request->headers->get('referer');

        if (empty($urlReferer)) {
            return null;
        }

        try {
            $requestNew = Request::create($urlReferer);
            $routeRefererMatch = $this->router->match($requestNew->getPathInfo());

            return new RequestRefererDto(
                $routeRefererMatch['_route'],
                array_filter(
                    $routeRefererMatch,
                    fn (string $key) => !in_array($key, ['_route', '_controller']),
                    ARRAY_FILTER_USE_KEY
                )
            );
        } catch (NoConfigurationException|ResourceNotFoundException|MethodNotAllowedException) {
            return null;
        }
    }
}
