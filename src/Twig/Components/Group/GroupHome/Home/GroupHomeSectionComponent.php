<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupHomeSectionComponent',
    template: 'Components/Group/GroupHome/Home/GroupHomeSectionComponent.html.twig'
)]
class GroupHomeSectionComponent extends TwigComponent
{
    public GroupHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupHomeSectionComponent';
    }

    public function mount(GroupHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
