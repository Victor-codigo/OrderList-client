<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NavigationBarComponent',
    template: 'Components/NavigationBar/NavigationBarComponent.html.twig'
)]
class NavigationBarComponent extends TwigComponent
{
    private readonly RouterInterface $router;
    public NavigationBarLangDto $lang;
    public NavigationBarDto|TwigComponentDtoInterface $data;

    public readonly string $cssType;
    public readonly string $cssTextColor;

    public readonly array $sections;
    public readonly string $languageToggleUrl;

    protected static function getComponentName(): string
    {
        return 'NavigationBarComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator, RouterInterface $router)
    {
        parent::__construct($request, $translator);
        $this->router = $router;
    }

    public function mount(NavigationBarDto $data): void
    {
        $this->data = $data;

        $sections = $this->createSections($this->data);
        $this->languageToggleUrl = $this->createLanguageToggleUrl($this->data->routeName, $this->data->routeParameters, $this->data->locale);
        $this->sections = $sections;
    }

    private function createSections(NavigationBarDto $data): array
    {
        if (null === $data->groupNameEncoded || null === $data->sectionActiveId) {
            return [];
        }

        $sections[] = $this->createSectionListOrders($data->sectionActiveId);
        $sections[] = $this->createSectionProducts($data->sectionActiveId);
        $sections[] = $this->createSectionShops($data->sectionActiveId);

        return array_filter($sections);
    }

    private function createSectionListOrders(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.list_orders.label'),
            $this->translate('navigation.section.list_orders.title'),
            $this->router->generate('list_orders_home', [
                'group_name' => $this->data->groupNameEncoded,
                'section' => 'list-orders',
                'page' => 1,
                'page_items' => 100,
            ]),
            'list-orders' === $sectionActiveId || 'orders' === $sectionActiveId ? true : false
        );
    }

    private function createSectionProducts(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.products.label'),
            $this->translate('navigation.section.products.title'),
            $this->router->generate('product_home', [
                'group_name' => $this->data->groupNameEncoded,
                'section' => 'product',
                'page' => 1,
                'page_items' => 100,
            ]),
            'product' === $sectionActiveId ? true : false
        );
    }

    private function createSectionShops(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.shops.label'),
            $this->translate('navigation.section.shops.title'),
            $this->router->generate('shop_home', [
                'group_name' => $this->data->groupNameEncoded,
                'section' => 'shop',
                'page' => 1,
                'page_items' => 100,
            ]),
            'shop' === $sectionActiveId ? true : false
        );
    }

    private function createLanguageToggleUrl(string $routeName, array $routeParameters, string $locale): string
    {
        unset($routeParameters['_locale']);

        return $this->router->generate($routeName, [
            '_locale' => 'en' === $locale ? 'es' : 'en',
            ...$routeParameters,
        ]);
    }
}
