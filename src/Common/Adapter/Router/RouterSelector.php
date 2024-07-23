<?php

declare(strict_types=1);

namespace Common\Adapter\Router;

use Common\Domain\Config\Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\MissingMandatoryParametersException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RouterInterface;

class RouterSelector
{
    private RouterInterface $router;
    private Request $request;
    private ?string $locale;
    private ?string $groupNameEncoded;
    private ?string $section;
    private ?string $listOrdersName;
    private ?string $page;
    private ?string $pageItems;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->request = $requestStack->getMainRequest();
        $this->locale = $this->request->attributes->get('_locale');
        $this->groupNameEncoded = $this->request->attributes->get('group_name');
        $this->section = $this->request->attributes->get('section');
        $this->listOrdersName = $this->request->attributes->get('list_orders_name');
        $this->page = $this->request->attributes->get('page');
        $this->pageItems = $this->request->attributes->get('page_items');
    }

    /**
     * Adds suffix to route name, [_no_group|_group].
     */
    public function getRouteNameWithSuffix(string $routeNameWithoutSuffix): string
    {
        return null === $this->groupNameEncoded
            ? "{$routeNameWithoutSuffix}_no_group"
            : "{$routeNameWithoutSuffix}_group";
    }

    /**
     * @return array<{
     *  '_locale': string
     *  'group_name': string,
     *  'section': string,
     *  'page': string,
     *  'page_items': string,
     * }>
     */
    private function getDefaultParameters(): array
    {
        $parametersDefault = [];

        if (null !== $this->locale) {
            $parametersDefault['_locale'] = $this->locale;
        }

        if (null !== $this->groupNameEncoded) {
            $parametersDefault['group_name'] = $this->groupNameEncoded;
        }

        if (null !== $this->section) {
            $parametersDefault['section'] = $this->section;
        }

        if (null !== $this->page) {
            $parametersDefault['page'] = 1;
        }

        if (null !== $this->pageItems) {
            $parametersDefault['page_items'] = Config::PAGINATION_ITEMS_MAX;
        }

        return $parametersDefault;
    }

    public function generateRouteWithDefaults(string $routeName, array $parameters): string
    {
        $routeParameters = [
            ...$this->getDefaultParameters(),
            ...$parameters,
        ];

        return $this->generateRoute($routeName, $routeParameters);
    }

    public function generateRoute(string $routeName, array $parameters): string
    {
        $routeNameToGenerate = $routeName;
        if (!$this->routeNameExists($routeName)) {
            $routeNameToGenerate = $this->getRouteNameWithSuffix($routeName);
        }

        return $this->router->generate($routeNameToGenerate, $parameters);
    }

    private function routeNameExists($routeName): bool
    {
        try {
            $this->router->generate($routeName);

            return true;
        } catch (RouteNotFoundException) {
            return false;
        } catch (MissingMandatoryParametersException) {
            return true;
        }
    }
}
