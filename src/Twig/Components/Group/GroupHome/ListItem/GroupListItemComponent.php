<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupListItemComponent',
    template: 'Components/Group/GroupHome/ListItem/GroupListItemComponent.html.twig'
)]
final class GroupListItemComponent extends HomeListItemComponent
{
    use UrlEncoder;

    public const GROUP_USERS_NAME_PLACEHOLDER = '--group_users_name--';

    public HomeListItemComponentLangDto $lang;
    public GroupListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $groupDataJson;
    public readonly string $urlGroupUsers;

    public static function getComponentName(): string
    {
        return 'GroupListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->urlGroupUsers = $this->parseItemUrlListItemsPlaceholder($this->data->urlLinkGroupUsers, $this->data->name);
        $this->groupDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new GroupListItemComponentLangDto(
            $this->translate('group_modify_button.title'),
            $this->translate('group_remove_button.title'),
            $this->translate('group_info_button.title'),
            $this->translate('group_group_users_button.title'),
            $this->translate('group_image.alt'),
            $this->translate('group_image.title'),
            $this->translate('admin.label'),
        );
    }

    /**
     * @throws JsonException
     */
    private function parseItemDataToJson(GroupListItemComponentDto $groupData): string
    {
        $groupDataToParse = [
            'id' => $groupData->id,
            'name' => $groupData->name,
            'description' => $groupData->description,
            'image' => $groupData->image,
            'noImage' => $groupData->noImage,
            'createdOn' => $groupData->createdOn->format('Y-m-d'),
            'admin' => $groupData->admin,
        ];

        return json_encode($groupDataToParse, JSON_THROW_ON_ERROR);
    }

    private function parseItemUrlListItemsPlaceholder(string $urlGroupUsersPlaceholder, string $groupUsersName): string
    {
        $groupUsersNameDecoded = $this->encodeUrl($groupUsersName);

        return mb_ereg_replace(self::GROUP_USERS_NAME_PLACEHOLDER, $groupUsersNameDecoded, $urlGroupUsersPlaceholder);
    }
}
