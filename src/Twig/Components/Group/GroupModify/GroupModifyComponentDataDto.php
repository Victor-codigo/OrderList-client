<?php

declare(strict_types=1);

namespace App\Twig\Components\Group\GroupModify;

use App\Twig\Components\Controls\DropZone\DropZoneComponentDto;
use App\Twig\Components\Controls\ImageAvatar\ImageAvatarComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class GroupModifyComponentDataDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly GroupModifyComponentDto $groupModify,
        public readonly ModalComponentDto $groupRemoveModal,
        public readonly DropZoneComponentDto $imageDropZoneDto,
        public readonly ImageAvatarComponentDto $imageAvatarDto
    ) {
    }
}
