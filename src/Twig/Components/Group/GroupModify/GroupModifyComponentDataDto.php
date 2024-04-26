<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupModify;

use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class GroupModifyComponentDataDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly GroupModifyComponentDto $groupModify,
        public readonly TitleComponentDto $titleDto,
        public readonly DropZoneComponentDto $imageDropZoneDto,
        public readonly ImageAvatarComponentDto $imageAvatarDto
    ) {
    }
}
