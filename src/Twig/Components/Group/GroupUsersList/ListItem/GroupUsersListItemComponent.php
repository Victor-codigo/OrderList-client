<?php

namespace App\Twig\Components\Group\GroupUsersList\ListItem;

use App\Twig\Components\Group\GroupList\ListItem\GroupListItemComponentDto;
use App\Twig\Components\Group\GroupList\ListItem\GroupListItemComponentLangDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersListItemComponent',
    template: 'Components/Group/GroupUsersList/ListItem/GroupUsersListItemComponent.html.twig'
)]
final class GroupUsersListItemComponent extends TwigComponent
{
    public const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;

    public GroupListItemComponentLangDto $lang;
    public GroupListItemComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupUsersListItemComponent';
    }

    public function mount(GroupUsersListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new GroupListItemComponentLangDto(
            $this->translate('group_image.alt'),
            $this->translate('group_remove.alt'),
        );
    }
}
