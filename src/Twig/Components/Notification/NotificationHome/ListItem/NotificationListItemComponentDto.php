<?php

declare(strict_types=1);

namespace App\Twig\Components\Notification\NotificationHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class NotificationListItemComponentDto extends HomeListItemComponentDto
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $notificationInfoModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $message,
        public readonly string $image,
        public readonly bool $viewed,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }
}
