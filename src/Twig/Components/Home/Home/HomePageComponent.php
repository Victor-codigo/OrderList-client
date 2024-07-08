<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

use App\Controller\Request\Response\UserDataResponse;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\Router\RouterSelector;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomePageComponent',
    template: 'Components/Home/Home/HomePageComponent.html.twig'
)]
class HomePageComponent extends TwigComponent
{
    use UrlEncoder;

    public HomePageComponentDto|TwigComponentDtoInterface $data;

    private readonly RouterSelector $routerSelector;

    public readonly ?UserButtonDto $userButton;
    public readonly ?MenuButtonDto $profileButton;
    public readonly ?MenuButtonDto $groupButton;
    public readonly ?MenuButtonDto $logoutButton;

    protected static function getComponentName(): string
    {
        return 'HomePageComponent';
    }

    public function __construct(RequestStack $request, TranslatorInterface $translator, RouterSelector $routerSelector)
    {
        parent::__construct($request, $translator);
        $this->routerSelector = $routerSelector;
    }

    public function mount(HomePageComponentDto $data): void
    {
        $this->data = $data;

        $this->userButton = $this->createUserButton($this->data->userData);
        $this->profileButton = $this->createProfileButton($this->data->userData);
        $this->groupButton = $this->createGroupButton($this->data->userData);
        $this->logoutButton = $this->createLogoutButton($this->data->userData);
    }

    private function createUserButton(?UserDataResponse $userData): ?UserButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new UserButtonDto(
            $userData->name,
            $userData->image,
            $this->translate('user_menu.user_menu.title'),
            $this->translate('user_menu.user_menu.alt'),
        );
    }

    private function createProfileButton(?UserDataResponse $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('user_menu.profile.label'),
            $this->translate('user_menu.profile.title'),
            $this->routerSelector->generateRoute('user_profile',
                [
                    'user_name' => $this->encodeUrl($userData->name),
                ]),
            $userData->image
        );
    }

    private function createGroupButton(?UserDataResponse $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('user_menu.groups.label'),
            $this->translate('user_menu.groups.title'),
            $this->routerSelector->generateRoute('group_home', [
                'section' => 'groups',
                'page' => 1,
                'page_items' => 100,
            ]),
            null
        );
    }

    private function createLogoutButton(?UserDataResponse $userData): ?MenuButtonDto
    {
        if (null === $userData) {
            return null;
        }

        return new MenuButtonDto(
            $this->translate('user_menu.logout.label'),
            $this->translate('user_menu.logout.title'),
            $this->routerSelector->generateRoute('user_logout', []),
            null
        );
    }
}
