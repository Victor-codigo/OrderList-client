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

    /**
     * @var GroupListItemComponentLangDto
     */
    public HomeListItemComponentLangDto $lang;
    public GroupListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $groupDataJson;
    public readonly string $urlGroupUsers;
    public readonly string $urlGroupSelectWithGroup;
    public readonly string $urlGroupSelectNoGroup;

    public static function getComponentName(): string
    {
        return 'GroupListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->urlGroupUsers = $this->parseItemUrlGroupUsersPlaceholder($this->data->urlLinkGroupUsers, $this->data->name);
        $this->urlGroupSelectWithGroup = $this->parseItemUrlGroupSelectPlaceholder($this->data->urlGroupSelectWithGroupPlaceholder, $this->data->name);
        $this->urlGroupSelectNoGroup = $this->parseItemUrlGroupSelectPlaceholder($this->data->urlGroupSelectNoGroupPlaceholder, $this->data->name);
        $this->groupDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new GroupListItemComponentLangDto(
            $this->translate('group_user.label'),
            $this->translate('group_user.description'),
            $this->translate('group_modify_button.title'),
            $this->translate('group_remove_button.title'),
            $this->translate('group_info_button.title'),
            $this->translate('group_group_users_button.title'),
            $this->translate('group_group_select_button.title'),
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
        $name = $groupData->name;
        $description = $groupData->description;
        if ('user' === $groupData->type) {
            $name = $this->lang->userGroupLabel;
            $description = $this->lang->userGroupDescription;
        }

        $groupDataToParse = [
            'id' => $groupData->id,
            'name' => $name,
            'description' => $description,
            'image' => $groupData->image,
            'noImage' => $groupData->noImage,
            'createdOn' => $groupData->createdOn->format('Y-m-d'),
            'admin' => $groupData->admin,
        ];

        return json_encode($groupDataToParse, JSON_THROW_ON_ERROR);
    }

    private function parseItemUrlGroupUsersPlaceholder(string $urlGroupUsersPlaceholder, string $groupUsersName): string
    {
        $groupUsersNameDecoded = $this->encodeUrl(mb_strtolower($groupUsersName));

        return mb_ereg_replace(self::GROUP_USERS_NAME_PLACEHOLDER, $groupUsersNameDecoded, $urlGroupUsersPlaceholder);
    }

    private function parseItemUrlGroupSelectPlaceholder(string $urlGroupSelectPlaceholder, string $groupName): string
    {
        $groupSelectNameDecoded = $this->encodeUrl(mb_strtolower($groupName));

        return mb_ereg_replace(self::GROUP_USERS_NAME_PLACEHOLDER, $groupSelectNameDecoded, $urlGroupSelectPlaceholder);
    }
}
