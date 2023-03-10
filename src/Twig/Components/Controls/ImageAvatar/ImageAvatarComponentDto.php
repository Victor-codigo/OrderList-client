<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ImageAvatar;

use App\Twig\Components\TwigComponentDtoInterface;

class ImageAvatarComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string|null $imageSrc = null,
        public readonly string|null $imageNoAvatar = null,
        public readonly string|null $imageAlt = null,
        ) {
    }
}
