<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupUsersHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'GroupUsersHomeSectionComponent',
    template: 'Components/Group/GroupUsersHome/Home/GroupUsersHomeSectionComponent.html.twig'
)]
class GroupUsersHomeSectionComponent extends TwigComponent
{
    public GroupUsersHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'GroupUsersHomeSectionComponent';
    }

    public function mount(GroupUsersHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
