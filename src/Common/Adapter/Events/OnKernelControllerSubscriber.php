<?php

declare(strict_types=1);

namespace Common\Adapter\Events;

use App\Controller\Request\RequestDto;
use App\Controller\Request\RequestRefererDto;
use Common\Adapter\Events\DataLoader\GroupDataLoader;
use Common\Adapter\Events\DataLoader\ListOrdersDataLoader;
use Common\Adapter\Events\DataLoader\NavigationBarDataLoader;
use Common\Adapter\Events\DataLoader\NotificationDataLoader;
use Common\Adapter\Events\DataLoader\OrderDataLoader;
use Common\Adapter\Events\DataLoader\ProductDataLoader;
use Common\Adapter\Events\DataLoader\ShopDataLoader;
use Common\Adapter\Events\DataLoader\UserDataLoader;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Config\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
    public function __construct(
        private Environment $twig,
        private RouterInterface $router,

        private UserDataLoader $userDataLoader,
        private NotificationDataLoader $notificationDataLoader,
        private NavigationBarDataLoader $navigationBarLoader,
        private GroupDataLoader $groupDataLoader,
        private ShopDataLoader $shopDataLoader,
        private ProductDataLoader $productDataLoader,
        private ListOrdersDataLoader $listOrdersDataLoader,
        private OrderDataLoader $orderDataLoader,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::CONTROLLER => ['__invoke']];
    }

    public function __invoke(ControllerEvent $event): void
    {
        $requestDto = $this->loadRequestDto($event->getRequest());
        $this->loadTwigGlobals($requestDto);
    }

    private function loadRequestDto(Request $request): RequestDto
    {
        $tokenSession = $this->loadTokenSession($request);
        $groupData = $this->groupDataLoader->load($request, $tokenSession);

        $requestDto = new RequestDto(
            $tokenSession,
            $this->loadLocale($request),
            $request->attributes->get('section'),
            $request->attributes->get('user_name'),
            $this->groupDataLoader->getGroupNameUrlEncoded($groupData),
            $request->attributes->get('list_orders_name'),
            $request->attributes->get('shop_name'),
            $request->attributes->get('product_name'),
            $this->loadPageData($request),
            $this->loadPageItemsData($request),
            $this->userDataLoader->load(...),
            $this->notificationDataLoader->load(...),
            $groupData,
            $this->shopDataLoader->load(...),
            $this->productDataLoader->load(...),
            $this->listOrdersDataLoader->load(...),
            $this->orderDataLoader->load(...),
            $request,
            $this->loadRefererRouteName($request)
        );

        $request->attributes->set('requestDto', $requestDto);

        return $requestDto;
    }

    private function loadTwigGlobals(RequestDto $requestDto): void
    {
        $navigationBarDto = $this->navigationBarLoader->load($requestDto);

        $this->twig->addGlobal('NavigationBarComponent', $navigationBarDto);
    }

    private function loadTokenSession(Request $request): ?string
    {
        if (!$request->cookies->has(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME)) {
            return null;
        }

        return $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
    }

    private function loadLocale(Request $request): string
    {
        if (!$request->attributes->has('_locale')) {
            return 'en';
        }

        return $request->attributes->get('_locale');
    }

    private function loadPageData(Request $request): ?int
    {
        if ($request->query->has('page')) {
            return $request->query->getInt('page');
        }

        if ($request->attributes->has('page')) {
            return $request->attributes->getInt('page');
        }

        return null;
    }

    private function loadPageItemsData(Request $request): ?int
    {
        if ($request->query->has('page_items')) {
            return $request->query->getInt('page_items');
        }

        if ($request->attributes->has('page_items')) {
            return $request->attributes->getInt('page_items');
        }

        return null;
    }

    private function loadRefererRouteName(Request $request): ?RequestRefererDto
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
        } catch (NoConfigurationException|ResourceNotFoundException|MethodNotAllowedException $e) {
            return null;
        }
    }
}
