<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersListItemComponent',
    template: 'Components/GroupUsers/GroupUsersHome/ListItem/GroupUsersListItemComponent.html.twig'
)]
final class GroupUsersListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public GroupUsersListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $groupUsersDataJson;

    public static function getComponentName(): string
    {
        return 'GroupUsersListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->groupUsersDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new GroupUsersListItemComponentLangDto(
            $this->translate('group_users_remove_button.title'),
            $this->translate('group_users_info_button.title'),

            $this->translate('group_users_grants_upgrade_button.title'),
            $this->translate('group_users_grants_downgrade_button.title'),
            $this->translate('group_users_grants_downgrade_button.msg_error_last_admin'),

            $this->translate('group_users_image.alt'),
            $this->translate('group_users_image.title'),
            $this->translate('admin.label'),
        );
    }

    private function parseItemDataToJson(GroupUsersListItemComponentDto $groupUsersData): string
    {
        $groupUsersDataToParse = [
            'id' => $groupUsersData->id,
            'name' => $groupUsersData->name,
            'image' => $groupUsersData->image,
            'noImage' => $groupUsersData->noImage,
            'admin' => $groupUsersData->admin,
        ];

        return json_encode($groupUsersDataToParse, JSON_THROW_ON_ERROR);
    }
}
