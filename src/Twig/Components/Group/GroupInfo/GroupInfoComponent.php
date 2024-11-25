<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponent;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentDto;
use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupInfoComponent',
    template: 'Components/Group/GroupInfo/GroupInfoComponent.html.twig'
)]
class GroupInfoComponent extends ItemInfoComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public GroupInfoComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupInfoComponent';
    }

    public function mount(ItemInfoComponentDto $data): void
    {
        $this->data = $data;
        $this->componentName = self::getComponentName();
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->lang = (new GroupInfoComponentLangDto())
            ->info(
                $this->translate('image.title'),
                $this->translate('image.alt'),
                $this->translate('created_on'),
                null
            )
            ->admin(
                $this->translate('admin.label')
            )
            ->description(
                $this->translate('description.label'),
            )
            ->priceHeaders(
                'No use',
                'No use',
                'No use',
            )
                ->shopsEmpty(
                    'No use',
                )
            ->buttons(
                $this->translate('close_button.label')
            )
            ->build();
    }
}
