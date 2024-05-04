<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUserInfoComponent',
    template: 'Components/GroupUsers/GroupUsersInfo/GroupUserInfoComponent.html.twig'
)]
class GroupUserInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public GroupUserInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupUserInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = self::getComponentName();
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new GroupUserInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
            )
            ->admin(
                $this->translate('admin.label')
            )
            ->description(
                'No used',
            )
            ->priceHeaders(
                'No used',
                'No used',
                'No used',
            )
            ->shopsEmpty(
                'No used'
            )
            ->buttons(
                $this->translate('close_button.label')
            )
            ->build();
    }
}
