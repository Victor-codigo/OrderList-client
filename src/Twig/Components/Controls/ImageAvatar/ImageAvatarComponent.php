<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ImageAvatar;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ImageAvatarComponent',
    template: 'Components/Controls/ImageAvatar/ImageAvatarComponent.html.twig'
)]
class ImageAvatarComponent extends TwigComponent
{
    public ImageAvatarComponentDto|TwigComponentDtoInterface $data;

    protected static function getComponentName(): string
    {
        return 'ImageAvatarComponent';
    }

    public function mount(ImageAvatarComponentDto $data): void
    {
        $this->data = $data;
    }
}
