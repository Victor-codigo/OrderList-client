<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupListItemComponent',
    template: 'Components/Group/GroupHome/ListItem/GroupListItemComponent.html.twig'
)]
final class GroupListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public GroupListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $groupDataJson;

    public static function getComponentName(): string
    {
        return 'GroupListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->groupDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new GroupListItemComponentLangDto(
            $this->translate('group_modify_button.alt'),
            $this->translate('group_modify_button.title'),
            $this->translate('group_remove_button.alt'),
            $this->translate('group_remove_button.title'),
            $this->translate('group_info_button.alt'),
            $this->translate('group_info_button.title'),
            $this->translate('group_image.alt'),
            $this->translate('group_image.title'),
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
        ];

        return json_encode($groupDataToParse, JSON_THROW_ON_ERROR);
    }
}
