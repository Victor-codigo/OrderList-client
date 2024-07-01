<?php

namespace App\Twig\Components\Group\GroupList\ListItem;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupListItemComponent_old',
    template: 'Components/Group/GroupList/ListItem/GroupListItemComponent.html.twig'
)]
final class GroupListItemComponent extends TwigComponent
{
    public const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;

    public GroupListItemComponentLangDto $lang;
    public GroupListItemComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupListItemComponent';
    }

    public function mount(GroupListItemComponentDto $data): void
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
