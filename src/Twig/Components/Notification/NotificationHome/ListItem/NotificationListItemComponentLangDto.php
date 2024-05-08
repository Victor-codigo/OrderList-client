<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;

class NotificationListItemComponentLangDto extends HomeListItemComponentLangDto
{
    public function __construct(
        public readonly string $removeItemButtonTitle,
        public readonly string $infoItemButtonTitle,

        public readonly string $imageItemAlt,
        public readonly string $imageItemTitle,
    ) {
    }
}
