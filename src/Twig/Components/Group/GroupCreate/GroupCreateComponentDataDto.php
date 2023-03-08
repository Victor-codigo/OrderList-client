<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupCreate;

use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class GroupCreateComponentDataDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly GroupCreateComponentDto $groupCreate,
        public readonly DropZoneComponentDto $imageDropZoneDto
    ) {
    }
}
