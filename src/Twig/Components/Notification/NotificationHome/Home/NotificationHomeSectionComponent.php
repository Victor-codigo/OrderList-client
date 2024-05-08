<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationHome\Home;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'NotificationHomeSectionComponent',
    template: 'Components/Notification/NotificationHome/Home/NotificationHomeSectionComponent.html.twig'
)]
class NotificationHomeSectionComponent extends TwigComponent
{
    public NotificationHomeSectionComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'NotificationHomeSectionComponent';
    }

    public function mount(NotificationHomeSectionComponentDto $data): void
    {
        $this->data = $data;
    }
}
