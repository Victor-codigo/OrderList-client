<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\CodedUrlParameter\UrlEncoder;
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
    use UrlEncoder;

    private const ROUTES_WITH_BACK_BUTTON = [
        'user_profile',
        'group_users_home',
        'order_home',
    ];

    private readonly RouterInterface $router;
    public NavigationBarLangDto $lang;
    public NavigationBarDto|TwigComponentDtoInterface $data;

    public readonly string $cssType;
    public readonly string $cssTextColor;

    public readonly array $sections;
    public readonly string $languageToggleUrl;

    public readonly string $backButtonTitle;

    public readonly ?UserButtonDto $userButton;

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
        $this->userButton = $this->createUserButton($this->data->routeName, $data->userData);
        $this->languageToggleUrl = $this->createLanguageToggleUrl($this->data->routeName, $this->data->routeParameters, $this->data->locale);
        $this->sections = $sections;
        $this->backButtonTitle = $this->translate('navigation.back_button.title');
    }

    public function hasBackButton(): bool
    {
        return in_array($this->data->routeName, self::ROUTES_WITH_BACK_BUTTON);
    }

    private function createSections(NavigationBarDto $data): array
    {
        if (null === $data->groupNameEncoded || null === $data->sectionActiveId) {
            return [];
        }

        $sections = [
            $this->createSectionGroups($data->sectionActiveId),
            $this->createSectionListOrders($data->sectionActiveId),
            $this->createSectionProducts($data->sectionActiveId),
            $this->createSectionShops($data->sectionActiveId),
        ];

        return array_filter($sections);
    }

    private function createSectionGroups(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.groups.label'),
            $this->translate('navigation.section.groups.title'),
            $this->router->generate('group_home', [
                'page' => 1,
                'page_items' => 100,
            ]),
            'groups' === $sectionActiveId ? true : false
        );
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

    private function createUserButton(string $routeName, ?UserDataResponse $userData): ?UserButtonDto
    {
        if (null === $userData) {
            return null;
        }

        if ('user_profile' === $routeName) {
            return null;
        }

        return new UserButtonDto(
            $userData->name,
            null === $userData->image
                ? null
                : HTTP_CLIENT_CONFIGURATION::API_DOMAIN."/{$userData->image}",
            $this->translate('navigation.profile.title'),
            $this->translate('navigation.profile.alt'),
            $this->router->generate('user_profile',
                [
                    'user_name' => $this->encodeUrl($userData->name),
                ])
        );
    }
}
