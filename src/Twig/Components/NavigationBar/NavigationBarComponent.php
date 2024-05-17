<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use App\Controller\Request\Response\NotificationDataResponse;
use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\Router\RouterSelector;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Symfony\Component\HttpFoundation\RequestStack;
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
        'group_home',
        'group_users_home',
        'order_home_group',
        'order_home_no_group',
        'notification_home',
    ];

    private const ROUTES_NO_SHOW_MENU_SECTIONS = [
        'user_profile',
        'group_home',
        'group_users_home',
        'notification_home',
    ];

    private readonly RouterSelector $routerSelector;
    public NavigationBarLangDto $lang;
    public NavigationBarDto|TwigComponentDtoInterface $data;

    public readonly string $cssType;
    public readonly string $cssTextColor;

    public readonly array $sections;
    public readonly string $languageToggleUrl;
    public readonly string $notificationUrl;

    public readonly string $backButtonTitle;

    public readonly int $notificationsNewNumber;

    public readonly ?UserButtonDto $userButton;
    public readonly ?MenuButtonDto $groupButton;
    public readonly ?MenuButtonDto $logoutButton;

    protected static function getComponentName(): string
    {
        return 'NavigationBarComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator, RouterSelector $routerSelector)
    {
        parent::__construct($request, $translator);
        $this->routerSelector = $routerSelector;
    }

    public function mount(NavigationBarDto $data): void
    {
        $this->data = $data;

        $sections = $this->createSections($this->data);
        $this->userButton = $this->createUserButton($data->userData);
        $this->notificationUrl = $this->createNotificationsUrl();
        $this->notificationsNewNumber = $this->getNotificationsNewCount($this->data->notificationsData);
        $this->groupButton = $this->createGroupButton($data->userData);
        $this->logoutButton = $this->createLogoutButton($data->userData);
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

        if (in_array($data->sectionActiveId, self::ROUTES_NO_SHOW_MENU_SECTIONS)) {
            return [];
        }

        $sections = [
            $this->createSectionListOrders($data->sectionActiveId),
            $this->createSectionProducts($data->sectionActiveId),
            $this->createSectionShops($data->sectionActiveId),
        ];

        return array_filter($sections);
    }

    private function createSectionListOrders(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.list_orders.label'),
            $this->translate('navigation.section.list_orders.title'),
            $this->routerSelector->generateListOrdersPath($this->data->groupType, $this->data->groupNameEncoded),
            'list-orders' === $sectionActiveId || 'orders' === $sectionActiveId ? true : false
        );
    }

    private function createSectionProducts(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.products.label'),
            $this->translate('navigation.section.products.title'),
            $this->routerSelector->generateProductPath($this->data->groupType, $this->data->groupNameEncoded),
            'product' === $sectionActiveId ? true : false
        );
    }

    private function createSectionShops(?string $sectionActiveId): NavigationBarSectionDto
    {
        return new NavigationBarSectionDto(
            $this->translate('navigation.section.shops.label'),
            $this->translate('navigation.section.shops.title'),
            $this->routerSelector->generateShopPath($this->data->groupType, $this->data->groupNameEncoded),
            'shop' === $sectionActiveId ? true : false
        );
    }

    private function createLanguageToggleUrl(string $routeName, array $routeParameters, string $locale): string
    {
        unset($routeParameters['_locale']);

        return $this->routerSelector->generate($routeName, [
            '_locale' => 'en' === $locale ? 'es' : 'en',
            ...$routeParameters,
        ]);
    }

    private function createNotificationsUrl(): string
    {
        return $this->routerSelector->generate('notification_home', [
            'section' => 'notifications',
            'page' => 1,
            'page_items' => 100,
        ]);
    }

    private function createUserButton(?UserDataResponse $userData): ?UserButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new UserButtonDto(
            $userData->name,
            $userData->image,
            $this->translate('navigation.profile.title'),
            $this->translate('navigation.profile.alt'),
            $this->routerSelector->generate('user_profile',
                [
                    'user_name' => $this->encodeUrl($userData->name),
                ])
        );
    }

    private function createGroupButton(?UserDataResponse $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('navigation.groups.label'),
            $this->translate('navigation.groups.title'),
            $this->routerSelector->generate('group_home', [
                'section' => 'groups',
                'page' => 1,
                'page_items' => 100,
            ])
        );
    }

    private function createLogoutButton(?UserDataResponse $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('navigation.logout.label'),
            $this->translate('navigation.logout.title'),
            $this->routerSelector->generate('user_logout')
        );
    }

    /**
     * @param NotificationDataResponse[] $notificationsData
     */
    private function getNotificationsNewCount(array $notificationsData): int
    {
        $notificationsNew = array_filter(
            $notificationsData,
            fn (NotificationDataResponse $notificationData) => !$notificationData->viewed
        );

        return count($notificationsNew);
    }
}
